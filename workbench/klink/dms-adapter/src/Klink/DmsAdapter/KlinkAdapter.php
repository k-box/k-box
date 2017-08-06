<?php

namespace Klink\DmsAdapter;

use KlinkDMS\Institution;
use KlinkDMS\Option;
use Illuminate\Support\Collection;

use Klink\DmsAdapter\Contracts\KlinkAdapter as AdapterContract;

use KlinkCoreClient;
use KlinkAuthentication;
use KlinkConfiguration;
use KlinkVisibilityType;
use KlinkDocumentDescriptor;
use KlinkDocument;

/**
 * Class to adapt the KlinkCoreClient to the DMS classes
 */
class KlinkAdapter implements AdapterContract
{
    
    /**
     * The K-Link configuration
     *
     * @var KlinkConfiguration
     */
    private $klink_config = null;

    /**
     * Client configured for connecting to the institution's K-Link Core
     * and the K-Link network (if configured)
     *
     * @var \KlinkCoreClient
     */
    private $connection = null;

    /**
     * The document types for the statistics
     *
     * @var array
     */
    private $documentTypes = ['document', 'presentation' , 'spreadsheet', 'image', 'web-page' ];

    /**
     * Creates a new KlinkAdapter instance.
     * Reads the static environment configuration and the option for
     * creating a KlinkCoreClient to connect both to the private
     * K-Core and to the network
     */
    public function __construct()
    {
        $cores = [
            new \KlinkAuthentication(\Config::get('dms.core.address'), \Config::get('dms.core.username'), \Config::get('dms.core.password'), \KlinkVisibilityType::KLINK_PRIVATE)
        ];
        
        try {
            $can_read_options = true;

            if (Option::option(Option::PUBLIC_CORE_ENABLED, false) && Option::option(Option::PUBLIC_CORE_CORRECT_CONFIG, false)) {
                try {
                    $cores[] = new \KlinkAuthentication(Option::option(Option::PUBLIC_CORE_URL), Option::option(Option::PUBLIC_CORE_USERNAME), @base64_decode(Option::option(Option::PUBLIC_CORE_PASSWORD)), \KlinkVisibilityType::KLINK_PUBLIC);
                } catch (\Exception $e) {
                    //TODO: launch some kind of events so the admin can see what happened
                    
                    \Log::error('Public Core configuration error', ['exception' => $e]);
                    Option::put(Option::PUBLIC_CORE_ENABLED, false);
                }
            }
        } catch (\Exception $qe) {
            $can_read_options = false;
            \Log::warning('Exception while reading K-Link Public core settings', ['exception' => $qe]);
        }

        $this->klink_config = new KlinkConfiguration(\Config::get('dms.institutionID'), \Config::get('dms.identifier'), $cores);
        
        if ($can_read_options && Option::option(Option::PUBLIC_CORE_DEBUG, false)) {
            $this->klink_config->enableDebug();
        }

        $this->connection = new KlinkCoreClient($this->klink_config, app('log'));
    }
    
    /**
     * Check if the network configuration is enabled
     *
     * @return bool
     * @uses Option::PUBLIC_CORE_ENABLED
     */
    public function isNetworkEnabled()
    {
        return ! ! Option::option(Option::PUBLIC_CORE_ENABLED, false);
    }

    /**
     *
     *
     * @uses \KlinkCoreClient::test
     * @return array containing a key result and error, the key result contains the return
     *               value from {@see \KlinkCoreClient::test}, while the key error contains
     *               the eventual exception if the test fails
     */
    public function test(KlinkAuthentication $core = null)
    {
        $configuration = $this->klink_config;
        
        if (! is_null($core)) {
            $cores = [
                $core
            ];

            $configuration = new KlinkConfiguration(\Config::get('dms.institutionID'), \Config::get('dms.identifier'), $cores);
        }

        $error = null;
        $health = null;
        $result = KlinkCoreClient::test($configuration, $error, false, $health);

        return compact('result', 'error');
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
        
        if (is_null($cached)) {
            try {
                $core_inst = $this->connection->getInstitution($klink_id);

                $cached = Institution::fromKlinkInstitutionDetails($core_inst);
            } catch (\Exception $e) {
                \Log::error('Error get Institution from K-Link', ['context' => 'KlinkAdapter::getInstitution', 'param' => $klink_id, 'exception' => $e]);

                return $default;
            }
        }

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
        
        $connection = $this->connection;
        
        $insts = \Cache::remember('dms_institutions', 60, function () use ($connection, $cached, $columns) {
            try {
                $core_insts = $connection->getInstitutions();

                foreach ($core_insts as $inst) {
                    Institution::fromKlinkInstitutionDetails($inst);
                }

                return Institution::all($columns);
            } catch (Exception $e) {
                \Log::error('Error get Institutions from K-Link', ['context' => 'KlinkAdapter::getInstitutions', 'exception' => $e]);

                return $cached;
            }
        });
        
        
        if (! is_null($insts) && ! $insts->isEmpty()) {
            return $insts;
        }
        
        return $cached;
    }
    
    /**
     * Save the institution details on the K-Link Network
     *
     * @param Institution $institution the institution to save
     */
    public function saveInstitution(Institution $institution)
    {
        $this->connection->saveInstitution($institution->toKlinkInstitutionDetails());
    }
    
    /**
     * Delete the institution details from the K-Link Network.
     *
     * The institution is deleted according to the klink_id field value
     *
     * @param Institution $institution the institution to save
     */
    public function deleteInstitution(Institution $institution)
    {
        $this->connection->deleteInstitution($institution->klink_id);
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
        if (! $this->isNetworkEnabled() && $visibility==='public') {
            return 0;
        }

        try {
            $conn = $this->connection;

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
        $conn = $this->connection;

        if (! \Cache::has('dms_documents_statististics')) {
            $fs = \KlinkFacetsBuilder::create()->documentType()->build();

            $public_facets_response = [];

            $private_facets_response = $conn->facets($fs, 'private');

            $stats = $this->compactFacetResponse($public_facets_response, $private_facets_response);

            \Cache::put('dms_documents_statististics', $stats, 60);
        }
        
        return \Cache::get('dms_documents_statististics');
    }

    private function mapFacetItemToKeyValue(\KlinkFacetItem $item)
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
        return $this->connection->search($terms, $type, $resultsPerPage, $offset, $facets);
    }

    public function facets($facets, $visibility = KlinkVisibilityType::KLINK_PRIVATE, $term = '*')
    {
        return $this->connection->facets($facets, $visibility, $term);
    }

    public function getDocument($institutionId, $documentId, $visibility = KlinkVisibilityType::KLINK_PRIVATE)
    {
        return $this->connection->getDocument($institutionId, $documentId, $visibility);
    }

    public function updateDocument(KlinkDocument $document)
    {
        return $this->connection->updateDocument($document);
    }

    public function removeDocumentById($institution, $document, $visibility = KlinkVisibilityType::KLINK_PRIVATE)
    {
        return $this->connection->removeDocumentById($institution, $document, $visibility);
    }

    public function removeDocument(KlinkDocumentDescriptor $document)
    {
        return $this->connection->removeDocument($document);
    }

    public function addDocument(KlinkDocument $document)
    {
        return $this->connection->addDocument($document);
    }

    public function generateThumbnailOfWebSite($url, $image_file = null)
    {
        return $this->connection->generateThumbnailOfWebSite($url, $image_file);
    }

    public function generateThumbnailFromContent($mimeType, $data)
    {
        return $this->connection->generateThumbnailFromContent($mimeType, $data);
    }
}
