<?php

namespace Klink\DmsAdapter\Contracts;

use Illuminate\Support\Collection;

use KSearchClient\Http\Authentication;
use Klink\DmsAdapter\KlinkVisibilityType;
use Klink\DmsAdapter\KlinkDocumentDescriptor;
use Klink\DmsAdapter\KlinkDocument;

/**
 * Define a KlinkAdapter.
 *
 *
 * if you need an implementation type hint your
 * constructor with this interface
 */
interface KlinkAdapter
{

    // /**
    //  * Performs a search on K-Link Core
    //  *
    //  * @param string $terms the phrase or terms to search for
    //  * @param KlinkVisibilityType $type the type of the search to be perfomed, if null is specified the default behaviour is @see KlinkVisibilityType::KLINK_PRIVATE
    //  * @param int $resultsPerPage the number of results per page
    //  * @param int $offset the page to display
    //  * @param KlinkFacet[] $facets The facets that needs to be retrieved or what will be retrieved. Default null, no facets will be calculated or filtered.
    //  * @return KlinkSearchResult returns the document that match the searched terms
    //  * @throws KlinkException if something wrong happened during the communication with the core
    //  */
    // public function search($terms, $type = KlinkVisibilityType::KLINK_PRIVATE, $resultsPerPage = 10, $offset = 0, $facets = null);

    // /**
    //  * Retrieve only the specified facets from the available documents that has the specified visibility
    //  *
    //  * to construct the facets parameter @see KlinkFacetsBuilder
    //  *
    //  * @param KlinkFacet[]|string[] $facets The facets to be retrieved. You can pass also an array of string with the facet names, the default configuration will be applyied
    //  * @param string $visibility The visibility. Default @see KlinkVisibilityType::KLINK_PRIVATE
    //  */
    // public function facets($facets, $visibility = KlinkVisibilityType::KLINK_PRIVATE, $term = '*');
    
    // /**
    //  * Get the registered Institutions
    //  *
    //  * @param string $id The K-Link ID of the institution to find. Default null, all known institutions are returned
    //  * @param mixed $default The default value to return in case the requested institution cannot be found. This parameter is ignored if $id is null
    //  * @return Collection|Institution|null the known institutions. If the $id is passed the single institution is returned, if found
    //  * @deprecated
    //  */
    // public function institutions($id = null, $default = null);

    // /**
    //  * Get the institutions name given the K-Link Identifier
    //  * @param  string $klink_id The K-Link institution identifier
    //  * @return string           The name of the institution if exists, otherwise the passed id is returned
    //  * @deprecated
    //  */
    // public function getInstitutionName($klink_id);

    /**
     * Retrieve the Document Descriptor of an indexed document given the institution identifier and the local document identifier
     * @param string $uuid
     * @param string $visibility (optional) The visibility of the document to be retrieved. Acceptable values are: public, private. Default value KlinkVisibilityType::KLINK_PRIVATE.
     * @return \KSearchClient\Model\Data\Data
     * @throws InvalidArgumentException If one or more parameters are invalid
     */
    public function getDocument($uuid, $visibility = KlinkVisibilityType::KLINK_PRIVATE);
    
    /**
     * Add a document to the K-Link Core.
     *
     * @param KlinkDocument $document
     * @return KlinkDocumentDescriptor
     * @throws KlinkException if something wrong happened during the communication with the core
     */
    public function addDocument(KlinkDocument $document);

    /**
     * Updates a previously added document.
     *
     * An existing KlinkDocumentDescriptor must be provided.
     *
     * @param KlinkDocument $document the new information about the document. The document descriptor must have the same ID of the already existing document
     * @return KlinkDocumentDescriptor
     * @throws KlinkException if something wrong happened during the communication with the core
     */
    public function updateDocument(KlinkDocument $document);

    /**
     * Remove a document given it's institution and local document identifier plus the it's visibility.
     *
     * If the visibility is not specified a KlinkVisibilityType::KLINK_PRIVATE is assumed
     *
     * Performs a document removal given directly the institutions identifier and the local document identifier.
     *
     * @param string $uuid
     * @param string $visibility (optional) The visibility of the document to be retrieved. Acceptable values are: public, private. Default value KlinkVisibilityType::KLINK_PRIVATE.
     * @return boolean
     * @throws InvalidArgumentException If one or more parameters are invalid
     * @internal
     */
    public function removeDocumentById($uuid, $visibility = KlinkVisibilityType::KLINK_PRIVATE);

    /**
     * Removes a previously added document given it's KlinkDocumentDescriptor
     * @param KlinkDocumentDescriptor $document
     * @return boolean
     * @throws KlinkException if something wrong happened during the communication with the core
     */
    public function removeDocument(KlinkDocumentDescriptor $document);

    

    /**
     * Test if the specified K-Search instance can be reached
     *
     * @param string $url The URL of the K-Search instance
     * @return mixed the test results
     */
    public function test($url, $username = null, $password = null);
}
