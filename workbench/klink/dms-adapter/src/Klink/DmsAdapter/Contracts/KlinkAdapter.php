<?php

namespace Klink\DmsAdapter\Contracts;

use Illuminate\Support\Collection;

use KlinkAuthentication;
use KlinkVisibilityType;
use KlinkDocumentDescriptor;
use KlinkDocument;

/**
 * Define a KlinkAdapter.
 *
 *
 * if you need an implementation type hint your
 * constructor with this interface
 */
interface KlinkAdapter
{

    /**
     * Performs a search on K-Link Core
     *
     * @param string $terms the phrase or terms to search for
     * @param KlinkVisibilityType $type the type of the search to be perfomed, if null is specified the default behaviour is @see KlinkVisibilityType::KLINK_PRIVATE
     * @param int $resultsPerPage the number of results per page
     * @param int $offset the page to display
     * @param KlinkFacet[] $facets The facets that needs to be retrieved or what will be retrieved. Default null, no facets will be calculated or filtered.
     * @return KlinkSearchResult returns the document that match the searched terms
     * @throws KlinkException if something wrong happened during the communication with the core
     */
    public function search($terms, $type = KlinkVisibilityType::KLINK_PRIVATE, $resultsPerPage = 10, $offset = 0, $facets = null);

    /**
     * Retrieve only the specified facets from the available documents that has the specified visibility
     *
     * to construct the facets parameter @see KlinkFacetsBuilder
     *
     * @param KlinkFacet[]|string[] $facets The facets to be retrieved. You can pass also an array of string with the facet names, the default configuration will be applyied
     * @param string $visibility The visibility. Default @see KlinkVisibilityType::KLINK_PRIVATE
     */
    public function facets($facets, $visibility = KlinkVisibilityType::KLINK_PRIVATE, $term = '*');

    /**
     * Check if the network configuration is enabled
     *
     * @return bool
     */
    public function isNetworkEnabled();
    
    /**
     * Get the registered Institutions
     *
     * @param string $id The K-Link ID of the institution to find. Default null, all known institutions are returned
     * @param mixed $default The default value to return in case the requested institution cannot be found. This parameter is ignored if $id is null
     * @return Collection|Institution|null the known institutions. If the $id is passed the single institution is returned, if found
     */
    public function institutions($id = null, $default = null);

    /**
     * Get the institutions name given the K-Link Identifier
     * @param  string $klink_id The K-Link institution identifier
     * @return string           The name of the institution if exists, otherwise the passed id is returned
     */
    public function getInstitutionName($klink_id);

    /**
     * Retrieve the Document Descriptor of an indexed document given the institution identifier and the local document identifier
     * @param string $institutionId
     * @param string $documentId
     * @param string $visibility (optional) The visibility of the document to be retrieved. Acceptable values are: public, private. Default value KlinkVisibilityType::KLINK_PUBLIC.
     * @return KlinkDocumentDescriptor
     * @throws InvalidArgumentException If one or more parameters are invalid
     */
    public function getDocument($institutionId, $documentId, $visibility = null);

    /**
     * Updates a previously added document.
     *
     * An existing KlinkDocumentDescriptor must be provided.
     *
     * @param KlinkDocument $document the new information about the document. The document descriptor must have the same ID of the already existing document
     * @param type $document_content
     * @return KlinkDocumentDescriptor
     * @throws KlinkException if something wrong happened during the communication with the core
     */
    public function updateDocument(KlinkDocument $document);

    /**
     * Remove a document given it's institution and local document identifier plus the it's visibility.
     *
     * If the visibility is not specified a KlinkVisibilityType::KLINK_PUBLIC is assumed
     *
     * Performs a document removal given directly the institutions identifier and the local document identifier.
     *
     * @param string $institution the institution identifier
     * @param string $document the local document identifier
     * @param string $visibility (optional) The visibility of the document to be retrieved. Acceptable values are: public, private. Default value KlinkVisibilityType::KLINK_PUBLIC.
     * @return boolean
     * @throws InvalidArgumentException If one or more parameters are invalid
     * @internal
     */
    public function removeDocumentById($institution, $document, $visibility = null);

    /**
     * Removes a previously added document given it's KlinkDocumentDescriptor
     * @param KlinkDocumentDescriptor $document
     * @return boolean
     * @throws KlinkException if something wrong happened during the communication with the core
     */
    public function removeDocument(KlinkDocumentDescriptor $document);

    /**
     * Add a document to the K-Link Core.
     *
     * @param KlinkDocument $document
     * @param type $document_content
     * @return KlinkDocumentDescriptor
     * @throws KlinkException if something wrong happened during the communication with the core
     */
    public function addDocument(KlinkDocument $document);

    /**
     * Generate a thumbnail of the given URL. Only web pages are supported.
     *
     * @param string $url the url of the page for the screenshot
     * @param string $image_file If specified is the path in which the file will be saved. Put null if you want the data back as the function return (default: null)
     * @return string|int|boolean The image content in PNG format if $image_file is null, the return of file_put_contents if a file path is specified
     * @throws InvalidArgumentException If the specified URL is not well formed
     * @throws KlinkException If the mimetype is not compatible with the thumbnail generator or the thumbnail cannot be generated
     */
    public function generateThumbnailOfWebSite($url, $image_file = null);

    /**
     * Generate a document thumbnail from the content of a file.
     *
     * The file content MUST NOT be encoded in base64 format
     *
     * @param  string  $mimeType      The mime type of the data that needs the thumbnail
     * @param  string|resource  $data The document data used for the thumbnail generation
     * @return string|boolean         The image content in PNG format or false in case of error
     * @internal
     */
    public function generateThumbnailFromContent($mimeType, $data);

    /**
     * Test if the configuration for the K-Core connection is valid
     *
     * If no parameter is specified the default DMS K-Core configuration is used
     *
     * @param KlinkAuthentication $core The core configuration to test,
     *        if null the DMS configured private K-Core is used. Default null.
     * @return mixed the test results
     */
    public function test(KlinkAuthentication $core = null);
}
