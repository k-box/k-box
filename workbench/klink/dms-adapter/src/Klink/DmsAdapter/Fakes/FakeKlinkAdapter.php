<?php

namespace Klink\DmsAdapter\Fakes;

use KlinkDMS\Institution;
use KlinkDMS\Option;
use Illuminate\Support\Collection;

use Klink\DmsAdapter\Contracts\KlinkAdapter as AdapterContract;

use Klink\DmsAdapter\KlinkSearchResults;
use Klink\DmsAdapter\KlinkSearchResultItem;
use Klink\DmsAdapter\KlinkDocument;
use Klink\DmsAdapter\KlinkDocumentDescriptor;
use Klink\DmsAdapter\KlinkVisibilityType;
use KSearchClient\Http\Authentication;
use Klink\DmsAdapter\KlinkFacetsBuilder;
use Klink\DmsAdapter\KlinkFacetItem;

use Faker\Factory as FakerFactory;
use PHPUnit_Framework_Assert as PHPUnit;

/**
 * FakeKlinkAdapter. Simulates a KlinkAdapter that always return positive responses.
 *
 * This might be useful in case you want to know if a method is called
 */
class FakeKlinkAdapter implements AdapterContract
{
    
    /**
     * @var array
     */
    private $calls;
    
    /**
     * @var array
     */
    private $documents;

    /**
     * @var Faker\Generator
     */
    private static $faker;

    /**
     * Creates a new FakeKlinkAdapter instance.
     */
    public function __construct()
    {
        $this->calls = [];
        $this->documents = [];
        self::$faker = FakerFactory::create();
    }
    

    
    public function test($url, $username = null, $password = null)
    {
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
        return Institution::where('klink_id', $id)->first();
    }

    /**
     * Get the institutions name given the K-Link Identifier
     * @param  string $klink_id The K-Link institution identifier
     * @return string           The name of the institution if exists, otherwise the passed id is returned
     */
    public function getInstitutionName($klink_id)
    {
        return $klink_id;
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
        return 24;
    }


    public function search($terms, $type = KlinkVisibilityType::KLINK_PRIVATE, $resultsPerPage = 10, $offset = 0, $facets = null)
    {
        return self::generateSearchResponse($terms, $type, $resultsPerPage, $offset, $facets);
    }

    public function facets($facets, $visibility = KlinkVisibilityType::KLINK_PRIVATE, $term = '*')
    {
        return self::generateFacetsResponse($facets, $visibility, $term);
    }

    public function getDocument($uuid, $visibility = KlinkVisibilityType::KLINK_PRIVATE)
    {
        if (isset($this->documents[$uuid.'-'.$visibility])) {
            return $this->documents[$uuid.'-'.$visibility];
        }

        return null;
    }

    public function updateDocument(KlinkDocument $document)
    {
        $this->countIndexing($document->getDescriptor()->uuid());

        $this->documents[$document->getDescriptor()->uuid().'-'.$document->getDescriptor()->getVisibility()] = $document->getDescriptor();

        return $document->getDescriptor();
    }

    public function removeDocumentById($uuid, $visibility = KlinkVisibilityType::KLINK_PRIVATE)
    {
        $this->countRemoval($uuid, $visibility);

        unset($this->documents[$uuid.'-'.$visibility]);
        
        return true;
    }

    public function removeDocument(KlinkDocumentDescriptor $document)
    {
        return $this->removeDocumentById($document->uuid(), $document->getVisibility());
    }

    public function addDocument(KlinkDocument $document)
    {
        $this->countIndexing($document->getDescriptor()->uuid());

        $this->documents[$document->getDescriptor()->uuid().'-'.$document->getDescriptor()->getVisibility()] = $document->getDescriptor();

        return $document->getDescriptor();
    }

    public static function generateSearchResponse($terms, $type = KlinkVisibilityType::KLINK_PRIVATE, $resultsPerPage = 10, $offset = 0, $facets = null)
    {
        if (! static::$faker) {
            static::$faker = FakerFactory::create();
        }

        $fakeResults = [];
        $fakeResultItem = null;

        for ($i=0; $i < $resultsPerPage; $i++) {
            $fakeResultItem = new KlinkSearchResultItem();
            $fakeResultItem->score = self::$faker->randomFloat(2, 0, 1);
            $fakeResultItem->document_descriptor = KlinkDocumentDescriptor::create(
                'KLINK',
                substr(self::$faker->sha256, 0, 6),
                self::$faker->sha256.self::$faker->sha256,
                self::$faker->sentence,
                self::$faker->mimeType,
                self::$faker->url,
                self::$faker->url,
                self::$faker->safeEmail,
                self::$faker->safeEmail,
                $type);

            $fakeResults[] = $fakeResultItem;
        }

        

        $attributes = [
            'query' => [
                'search' => $terms,
                'limit' => $resultsPerPage,
                'offset' => $offset,
                'filters' => '',
                'aggregations' => ! is_null($facets) ? self::generateFacetsResponse($facets) : self::generateFacetsResponse(KlinkFacetsBuilder::all()->build())
            ],
            'items' => $fakeResults,

        ];
        
        return KlinkSearchResults::fake($attributes, $type);
    }

    public static function generateFacetsResponse($facets, $visibility = KlinkVisibilityType::KLINK_PRIVATE, $term = '*')
    {
        if (! static::$faker) {
            static::$faker = FakerFactory::create();
        }

        $facetItem = null;

        foreach ($facets as $facet) {
            $facetItem = new KlinkFacetItem();
            $facetItem->term = static::$faker->name;
            $facetItem->count = static::$faker->randomDigit;

            $facet->items = [$facetItem];
        }

        return $facets;
    }

    /**
     * @param string $doc
     */
    private function countIndexing($doc)
    {
        $indexing = isset($this->calls['indexing']) ? $this->calls['indexing'] : [];

        $indexing[] = $doc;

        $this->calls['indexing'] = $indexing;
    }
    
    /**
     * @param string $doc
     * @param string $visibility
     */
    private function countRemoval($doc, $visibility)
    {
        $removing = isset($this->calls['removal']) ? $this->calls['removal'] : [];

        $removing[] = ['doc' => $doc, 'visibility' => $visibility];

        $this->calls['removal'] = $removing;
    }

    /**
     * Assert that a document identified by a K-Link ID has been added or updated
     *
     * @param string $documentId the descriptors' Local Document Id
     * @param int $times if more than 1 assert that the same document has been subject to add or update $times times
     */
    public function assertDocumentIndexed($documentId, $times = 1)
    {
        PHPUnit::assertNotEmpty($this->calls['indexing'], "KlinkAdapter addDocument or updateDocument not called");

        $filtered = array_filter($this->calls['indexing'], function ($el) use ($documentId) {
            return $el === $documentId;
        });

        PHPUnit::assertCount($times,

            $filtered,

            "The expected [{$documentId}] document was not indexed $times times."

        );
    }
    
    /**
     * Assert that a document identified by a K-Link ID and a visibility has been removed
     *
     * @param string $documentId the descriptors' Local Document Id
     * @param string $visibility the descriptors' visibility
     * @param int $times if more than 1 assert that the same document has been subject to add or update $times times
     */
    public function assertDocumentRemoved($documentId, $visibility, $times = 1)
    {
        if ($times == 0 && ! isset($this->calls['removal'])) {
            return;
        }

        PHPUnit::assertNotEmpty($this->calls['removal'], "KlinkAdapter removeDocumentById or removeDocument not called");

        $filtered = array_filter($this->calls['removal'], function ($el) use ($documentId, $visibility) {
            return $el['doc'] === $documentId && $el['visibility'] === $visibility;
        });

        PHPUnit::assertCount($times,

            $filtered,

            "The expected [{$documentId}, {$visibility}] document was not removed $times times."

        );
    }
}
