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
use KSearchClient\Model\Data\Data;

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

    private function selectConnection($visibility)
    {
        if(isset($this->connections[$visibility])){
            return $this->connections[$visibility];
        }

        throw new \Exception("No connection configured for visibility {$visibility}");
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

    /**
     * Get the registered Institutions
     *
     * @param string $id The K-Link ID of the institution to find. Default null, all known institutions are returned
     * @param mixed $default The default value to return in case the requested institution cannot be found. This parameter is ignored if $id is null
     * @return Collection|Institution|null the known institutions. If the $id is passed the single institution is returned, if found
     */
    public function institutions($id = null, $default = null)
    {
        if (! is_null($id)) {
            return $this->getInstitution($id, $default);
        } else {
            return $this->getInstitutions();
        }
    }

    /**
     * Get the institutions name given the K-Link Identifier
     * @param  string $klink_id The K-Link institution identifier
     * @return string           The name of the institution if exists, otherwise the passed id is returned
     */
    public function getInstitutionName($klink_id)
    {
        $cached = $this->getInstitution($klink_id, $klink_id);

        return is_string($cached) ? $cached : $cached->name;
    }

    /**
     * Retrieve an institution given its K-Link identifier
     * @param  string $klink_id the K-Link Id
     * @return \KlinkDMS\Institution|null the instance of Institution that corresponds to the given id or null if the institution is unknown or the id is not valid
     */
    private function getInstitution($klink_id, $default = null)
    {
        $cached = Institution::findByKlinkID($klink_id);

        return $cached;
    }

    /**
     * Get all the institutions currently available in the network.
     *
     * This method also synchronize the cache of the institutions with the current info coming from the network
     *
     * @param  array  $columns The field to return from the {@see Institution} model. Default all fields
     * @return Collection Collection of {@see Institution}
     */
    private function getInstitutions($columns = ['*'], $forceSync = false)
    {
        $cached = Institution::all($columns);
        
        return $cached;
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
            $conn = $this->selectConnection($visibility);

            $value = \Cache::remember($visibility.'_documents_count', 15, function () use ($conn, $visibility) {
                \Log::info('Updating documents count cache for '.$visibility);
                
                $res = $conn->search('*', $visibility, 0, 0);

                return $res->getTotalResults();
            });
            
            return $value;
        } catch (\Exception $e) {
            \Log::error('Error getDocumentsCount', ['visibility' => $visibility, 'exception' => $e]);

            return 0;
        }
    }

    /**
     * Returns some documents statistics, like document types, aggregated
     * for public and private
     *
     * @return array
     */
    public function getDocumentsStatistics()
    {
        $conn = $this->selectConnection('private');

        if (! \Cache::has('dms_documents_statististics')) {
            $fs = KlinkFacetsBuilder::create()->documentType()->build();

            $public_facets_response = [];

            $private_facets_response = $conn->facets($fs, 'private');

            $stats = $this->compactFacetResponse($public_facets_response, $private_facets_response);

            \Cache::put('dms_documents_statististics', $stats, 60);
        }
        
        return \Cache::get('dms_documents_statististics');
    }

    private function mapFacetItemToKeyValue(KlinkFacetItem $item)
    {
        return [ $item->getTerm() => $item->getOccurrenceCount() ];
    }

    private function compactFacetResponse($public_response, $private_response)
    {
        $public = $this->getL2Keys(array_map([$this, 'mapFacetItemToKeyValue'], array_flatten(array_pluck($public_response, 'items'))));

        // the idea is document => count

        $private = $this->getL2Keys(array_map([$this, 'mapFacetItemToKeyValue'], array_flatten(array_pluck($private_response, 'items'))));

        $all = [];

        foreach ($this->documentTypes as $type) {
            $pu = isset($public[$type]) ? $public[$type] : 0;
            $pr = isset($private[$type]) ? $private[$type] : 0;
            $to = $pu + $pr;

            $all[$type]['public'] = $pu;
            $all[$type]['private'] = $pr;
            $all[$type]['total'] = $to;
        }

        return $all;
    }

    private function getL2Keys($array)
    {
        $result = [];
        foreach ($array as $sub) {
            $result = array_merge($result, $sub);
        }
        return $result;
    }

    public function search($terms, $type = KlinkVisibilityType::KLINK_PRIVATE, $resultsPerPage = 10, $offset = 0, $facets = null)
    {
        return $this->selectConnection($type)->search($terms, $type, $resultsPerPage, $offset, $facets);
    }

    public function facets($facets, $visibility = KlinkVisibilityType::KLINK_PRIVATE, $term = '*')
    {
        return $this->selectConnection($visibility)->facets($facets, $visibility, $term);
    }

    public function getDocument($uuid, $visibility = KlinkVisibilityType::KLINK_PRIVATE)
    {
        return $this->selectConnection($visibility)->get($uuid);
    }

    public function updateDocument(KlinkDocument $document)
    {
        return $this->addDocument($document);
    }

    public function removeDocumentById($uuid, $visibility = KlinkVisibilityType::KLINK_PRIVATE)
    {
        return $this->selectConnection($visibility)->delete($uuid);
    }

    public function removeDocument(KlinkDocumentDescriptor $descriptor)
    {
        return $this->removeDocumentById($descriptor->uuid(), $descriptor->visibility());
    }

    /**
     * Add a KlinkDocument
     * 
     * @return KlinkDocumentDescriptor
     */
    public function addDocument(KlinkDocument $document)
    {
        /**
         * @var KSearchClient\Model\Data\Data
         */
        $added_data = $this->selectConnection($document->getDescriptor()->getVisibility())
            ->add($document->getDescriptor()->toData(), 
                  $document->getDocumentData());

        return $document->getDescriptor();
    }
}
