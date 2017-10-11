<?php

namespace Klink\DmsAdapter;

use Klink\DmsAdapter\KlinkDocument;
use KlinkDMS\Option;
use KlinkDMS\Institution;
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

class KlinkAdapter implements AdapterContract
{
    /**
     * Contains the currently configured K-Search clients
     *
     * @var array
     */
    private $connections = null;

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
            KlinkVisibilityType::KLINK_PRIVATE => Client::build(config('dms.core.address'))
        ];
        
        try {
            $can_read_options = true;
            
            if (Option::option(Option::PUBLIC_CORE_ENABLED, false) && Option::option(Option::PUBLIC_CORE_CORRECT_CONFIG, false)) {
                
                // TODO: USERNAME should be the ORIGIN, so in theory the URL of the K-Box
                
                $this->connection[KlinkVisibilityType::KLINK_PUBLIC] = Client::build(Option::option(Option::PUBLIC_CORE_URL), new Authentication(Option::option(Option::PUBLIC_CORE_USERNAME), @base64_decode(Option::option(Option::PUBLIC_CORE_PASSWORD))));
                
            }
        } catch (\Exception $qe) {
            $can_read_options = false;
            \Log::warning('Exception while reading K-Link Network settings', ['exception' => $qe]);
        }
    }

    /**
     * Select the client instance to use based on the visibility.
     * 
     * - private visibility means local client
     * - public visibility means Network client
     */
    private function selectConnection($visibility)
    {
        if(isset($this->connections[$visibility])){
            return $this->connections[$visibility];
        }

        throw new \Exception("No connection configured for visibility {$visibility}");
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
        /**
         * @var KSearchClient\Model\Data\Data
         */
        $added_data = $this->selectConnection($document->getDescriptor()->getVisibility())
            ->add($document->getDescriptor()->toData(), 
                  $document->getDocumentData());

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
    public function test($url, $username = null, $password = null)
    {
        $authentication = null;

        if(!empty($username) || !empty($password)){
            $authentication = new Authentication($password, $username);
        }

        $client = Client::build($url, $authentication);

        return false;
    }
}
