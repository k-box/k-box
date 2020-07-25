<?php

namespace Klink\DmsAdapter;

use Log;
use Exception;
use Klink\DmsAdapter\KlinkDocument;
use KBox\Option;
use KSearchClient\Client;
use Klink\DmsAdapter\KlinkDocumentDescriptor;
use Illuminate\Support\Collection;
use KSearchClient\Http\Authentication;
use Klink\DmsAdapter\KlinkVisibilityType;
use Klink\DmsAdapter\Contracts\KlinkAdapter as AdapterContract;
use Klink\DmsAdapter\KlinkFacetsBuilder;
use Klink\DmsAdapter\KlinkFacetItem;
use Klink\DmsAdapter\KlinkSearchResults;
use Klink\DmsAdapter\KlinkSearchRequest;
use KSearchClient\Model\Data\Data;
use KSearchClient\Model\Data\DataStatus;
use KSearchClient\Model\Search\SearchParams;
use KSearchClient\Model\Search\Aggregation;
use KSearchClient\Exception\ErrorResponseException;
use Klink\DmsAdapter\Concerns\HasConnections;
use Klink\DmsAdapter\Exceptions\KlinkException;

class KlinkAdapter implements AdapterContract
{
    use HasConnections;

    /**
     * The document types for the statistics
     *
     * @var array
     */
    private $documentTypes = ['document', 'presentation' , 'spreadsheet', 'image', 'web-page' ];

    /**
     * Creates a new KlinkAdapter instance.
     * Reads the static environment configuration and the option for
     * creating a KSearchClient to connect both to the private
     * K-Search and to the network
     */
    public function __construct()
    {
        $this->connections = [
            KlinkVisibilityType::KLINK_PRIVATE => Client::build(config('dms.core.address'), null)
        ];
        
        try {
            
            if (network_enabled()) {
                
                $this->connections[KlinkVisibilityType::KLINK_PUBLIC] = Client::build(
                    Option::option(Option::PUBLIC_CORE_URL), 
                    new Authentication(
                        @base64_decode(Option::option(Option::PUBLIC_CORE_PASSWORD)), 
                        config('app.url')));
                
            }
        } catch (\Exception $qe) {
            Log::warning('Exception while reading K-Link Network settings', ['exception' => $qe]);
        }
    }

    

    /**
     * @param KlinkSearchRequest $searchRequest
     * @return KlinkSearchResults
     */
    public function search(KlinkSearchRequest $searchRequest)
    {
        KlinkVisibilityType::fromString($searchRequest->visibility()); // check if a valid visibility is used

        $results = KlinkSearchResults::make(
            $this->selectConnection($searchRequest->visibility())->search($searchRequest->toSearchParams()), 
            $searchRequest->visibility());

        return $results;
    }

    /**
     * Retrieve the available aggregated terms for the specified aggregations
     * 
     * @param array $facets the aggregations to activate, see KlinkFacets
     * @return array the aggregation results
     */
    public function facets(array $facets, $visibility = KlinkVisibilityType::KLINK_PRIVATE, $terms = '*')
    {
        KlinkVisibilityType::fromString($visibility); // check if a valid visibility is used

        if(empty($terms)){
            // making sure in case a developer pass something is not null or empty string
            $terms = '*';
        }

        $aggregations = $this->search(KlinkSearchRequest::build($terms, $visibility, 1, 1, $facets));

        return $aggregations->getFacets();
    }

    /**
     * Returns the number of indexed documents with the respect to the visibility.
     *
     * Public visibility -> all documents inside the K-Link Network
     *
     * private visibility -> documents inside private K-Link Core
     *
     * This method uses caching, so be aware that the results you receive might be older than real time
     *
     * @param  string $visibility the visibility (if nothing is specified, a 'public' visibility is considered)
     * @return integer            the amount of documents indexed. It returns 0 also if the public network is
     *                            not enabled, but the public visibility is requested
     */
    public function getDocumentsCount($visibility = 'public')
    {
        if (! network_enabled() && $visibility==='public') {
            return 0;
        }

        try {

            $results = $this->search(KlinkSearchRequest::build('*', $visibility, 1, 1));

            return $results->getTotalResults();
            
        } catch (\Exception $e) {
            Log::error('Error getDocumentsCount', ['visibility' => $visibility, 'exception' => $e]);

            return 0;
        }
    }

    
    /**
     * Add a KlinkDocument
     * 
     * @return KlinkDocumentDescriptor
     * @throws KlinkException
     */
    public function addDocument(KlinkDocument $document)
    {
        KlinkVisibilityType::fromString($document->getDescriptor()->getVisibility()); // check if a valid visibility is used

        Log::info('Sending data.add request', ['data' => $document->getDescriptor()->toData(), 'withDataTextualContent' => !empty($document->getDocumentData())]);

        try{
            /**
             * @var KSearchClient\Model\Data\Data
             */
            $added_data = $this->selectConnection($document->getDescriptor()->getVisibility())
            ->add($document->getDescriptor()->toData(), 
            $document->getDocumentData());
            
        }catch(Exception $ex){
            throw new KlinkException($ex->getMessage(), $ex->getCode(), $ex);   
        }

        // checking if the indexing is going ahead
        $status = $this->getStatus($document->getDescriptor()->uuid(), $document->getDescriptor()->getVisibility());

        // considering that since version 3.3.0 of the K-Search even when index 
        // fails the document is searchable, we don't throw errors unless the 
        // download failed, otherwise we write warnings in the log
        if($status === DataStatus::STATUS_QUEUED_OK){
            Log::warning("The data {$document->getDescriptor()->uuid()} is still queued for processing after 6 seconds. Considering it ok.");
        }

        if($status === DataStatus::STATUS_DOWNLOAD_FAIL){
            throw new KlinkException("Data download failed for {$document->getDescriptor()->uuid()}");
        }

        if($status === DataStatus::STATUS_INDEX_FAIL){
            Log::warning("Indexing failed for {$document->getDescriptor()->uuid()}.");
        }

        return $document->getDescriptor();
    }

    /**
     * Internal status checking in accordance with API 3.4 workflow
     * 
     * 1. Checks processing status
     * 2. if queued for more than 5 seconds we give up
     * 3. if not anymore queued, check data status
     * 4. return the last available state
     * 
     * Take into consideration that processing status checking is a 
     * must because checking only the data status might return 
     * the status of the previously indexed version.
     * In addition the first check is done after 1 second to 
     * make sure that the K-Search enqueued the add request
     * 
     * @return string the status, @see DataStatus constants
     */
    private function getStatus($uuid, $visibility)
    {
        // first we check in the processing queue
        // if is there, at least something was received
        try{

            $statusResponse = null;
            $cycles = 0;
            do {
                sleep(1);
                $statusResponse = $this->selectConnection($visibility)->getStatus($uuid, DataStatus::TYPE_PROCESSING);
                $cycles++;
            }
            while($statusResponse->status === DataStatus::STATUS_QUEUED_OK && $cycles < 5);
            // check if is in the queue for a maximum of 5 cycles (~5 seconds)
        }catch(ErrorResponseException $ex){
            if($ex->getCode() !== 404){
                // not found might be plausible if the add request is not 
                // anymore in the queue to be picked up for processing.
                // On the other end we are not happy with other errors 
                // return "processing.failure";
                throw $ex;
            }
        }

        if($statusResponse && $statusResponse->status === DataStatus::STATUS_QUEUED_OK){
            // if is in the queue for 5 seconds, 
            // we give up and return the queued state
            return DataStatus::STATUS_QUEUED_OK;
        }

        // if is not in the processing queue, might be in the indexing stage, 
        // so we check DataStatus::TYPE_DATA and return whatever status is returned
        
        $statusResponse = $this->selectConnection($visibility)->getStatus($uuid, DataStatus::TYPE_DATA);
        
        Log::info("Status for $uuid in $visibility", ['status' => $statusResponse]);

        return $statusResponse->status;
    }

    public function getDocument($uuid, $visibility = KlinkVisibilityType::KLINK_PRIVATE)
    {
        KlinkVisibilityType::fromString($visibility); // check if a valid visibility is used

        return new KlinkSearchResultItem($this->selectConnection($visibility)->get($uuid));
    }

    public function updateDocument(KlinkDocument $document)
    {
        return $this->addDocument($document);
    }

    public function removeDocumentById($uuid, $visibility = KlinkVisibilityType::KLINK_PRIVATE)
    {
        KlinkVisibilityType::fromString($visibility); // check if a valid visibility is used

        return $this->selectConnection($visibility)->delete($uuid);
    }

    public function removeDocument(KlinkDocumentDescriptor $descriptor)
    {
        return $this->removeDocumentById($descriptor->uuid(), $descriptor->visibility());
    }

    /**
     * {@inherits}
     */
    public function canConnect($visibility = KlinkVisibilityType::KLINK_PRIVATE)
    {
        KlinkVisibilityType::fromString($visibility); // check if a valid visibility is used

        try{
            
            $this->selectConnection($visibility)->search(KlinkSearchRequest::build('*', 'private', 1, 1)->toSearchParams());

            return ['status' => 'ok'];

        }catch(\Exception $ex){

            return ['status' => 'error', 'error' => $ex->getMessage()];
        }
    }

    /**
     * {@inherits}
     */
    public static function test($url, $app_secret = null)
    {
        $authentication = null;

        if(!empty($app_secret)){
            $authentication = new Authentication($app_secret, config('app.url'));
        }

        $client = Client::build($url, $authentication);

        try{
            
            $client->search(KlinkSearchRequest::build('*', 'private', 1, 1)->toSearchParams());

            return ['status' => 'ok'];

        }catch(\Exception $ex){

            return ['status' => 'error', 'error' => $ex->getMessage()];
        }
    }
}
