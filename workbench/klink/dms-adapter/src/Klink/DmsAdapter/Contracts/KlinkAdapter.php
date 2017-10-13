<?php

namespace Klink\DmsAdapter\Contracts;

use Illuminate\Support\Collection;

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
     * Return the available connections configured
     * 
     * @return array
     */
    public function availableConnections();

    /**
     * Check if connection to the K-Search instance can be established
     * 
     * @param string $visibility The visibility of the connection to test. Default KlinkVisibilityType::KLINK_PRIVATE
     * @return array the test results. The array contains the 'status' key with value 'ok' or 'error' and an 'error' key in case of errors with the error reason
     */
    public function canConnect($visibility = KlinkVisibilityType::KLINK_PRIVATE);

    /**
     * Test if the specified K-Search instance can be reached
     * 
     * If the $app_secret is specified, the request will be 
     * authenticated using the currently configured 
     * application domain
     *
     * @param string $url The URL of the K-Search instance
     * @param string $app_secret The token to authenticate the request. Default null, no authentication will be sent.
     * @return array the test results. The array contains the 'status' key with value 'ok' or 'error' and an 'error' key in case of errors with the error reason
     */
    public static function test($url, $app_secret = null);
}
