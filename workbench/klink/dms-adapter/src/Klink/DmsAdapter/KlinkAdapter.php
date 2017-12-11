<?php

namespace Klink\DmsAdapter;

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
use KSearchClient\Model\Data\SearchParams;
use KSearchClient\Model\Data\Aggregation;
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
            \Log::warning('Exception while reading K-Link Network settings', ['exception' => $qe]);
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
     * private visibility -> documents inside institution K-Link Core
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
            \Log::error('Error getDocumentsCount', ['visibility' => $visibility, 'exception' => $e]);

            return 0;
        }
    }

    
    /**
     * Add a KlinkDocument
     * 
     * @return KlinkDocumentDescriptor
     */
    public function addDocument(KlinkDocument $document)
    {
        KlinkVisibilityType::fromString($document->getDescriptor()->getVisibility()); // check if a valid visibility is used

        \Log::info('Sending data.add request', ['data' => $document->getDescriptor()->toData(), 'dataTextualContent' => $document->getDocumentData()]);

        /**
         * @var KSearchClient\Model\Data\Data
         */
        $added_data = $this->selectConnection($document->getDescriptor()->getVisibility())
            ->add($document->getDescriptor()->toData(), 
                  $document->getDocumentData());

        // checking if the indexing is going ahead

        $status = 'queued';
        $cycles = 0;
        do {
            sleep(1);
            $status = $this->selectConnection($document->getDescriptor()->getVisibility())->getStatus($document->getDescriptor()->uuid());
            $cycles++;
        }
        while(strtolower($status->status) !== 'ok' && $cycles < 40);

        if(strtolower($status->status) !== 'ok'){
            throw new KlinkException('Indexing is still in progress after 40 seconds, aborting.');
        }

        return $document->getDescriptor();

    }

    public function getDocument($uuid, $visibility = KlinkVisibilityType::KLINK_PRIVATE)
    {
        KlinkVisibilityType::fromString($visibility); // check if a valid visibility is used

        return $this->selectConnection($visibility)->get($uuid);
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
