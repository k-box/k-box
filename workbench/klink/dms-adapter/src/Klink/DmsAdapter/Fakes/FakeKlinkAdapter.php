<?php

namespace Klink\DmsAdapter\Fakes;

use KBox\Option;
use Illuminate\Support\Collection;

use Klink\DmsAdapter\Contracts\KlinkAdapter as AdapterContract;
use KSearchClient\Model\Data\Data;
use KSearchClient\Client;
use Klink\DmsAdapter\KlinkSearchResults;
use Klink\DmsAdapter\KlinkSearchRequest;
use Klink\DmsAdapter\KlinkSearchResultItem;
use Klink\DmsAdapter\KlinkDocument;
use Klink\DmsAdapter\KlinkDocumentDescriptor;
use Klink\DmsAdapter\KlinkVisibilityType;
use KSearchClient\Http\Authentication;
use Klink\DmsAdapter\KlinkFacetsBuilder;
use Klink\DmsAdapter\KlinkFacetItem;
use Faker\Factory as FakerFactory;
use PHPUnit_Framework_Assert as PHPUnit;
use Klink\DmsAdapter\Concerns\HasConnections;
use KSearchClient\Model\Data\AggregationResult;
use KSearchClient\Model\Data\Author;
use KSearchClient\Model\Data\Uploader;
use KSearchClient\Model\Data\Copyright;
use KSearchClient\Model\Data\CopyrightOwner;
use KSearchClient\Model\Data\CopyrightUsage;
use KSearchClient\Model\Data\Properties;
use Mockery;

/**
 * FakeKlinkAdapter. Simulates a KlinkAdapter
 * whose responses are configurable.
 *
 * This might be useful in case you want to know if a method is called
 */
class FakeKlinkAdapter implements AdapterContract
{
    use HasConnections;
    
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

    private $search_results = [];

    /**
     * Creates a new FakeKlinkAdapter instance.
     */
    public function __construct()
    {
        $this->calls = [];
        $this->documents = [];
        self::$faker = FakerFactory::create();
        
        $this->connections = [
            KlinkVisibilityType::KLINK_PRIVATE => Mockery::mock(Client::class)
        ];
        
        try {
            
            if (Option::option(Option::PUBLIC_CORE_ENABLED, false) 
                && Option::option(Option::PUBLIC_CORE_CORRECT_CONFIG, false)) {
                
                $this->connections[KlinkVisibilityType::KLINK_PUBLIC] = Mockery::mock(Client::class);
                
            }
        } catch (\Exception $qe) {
            \Log::warning('Exception while reading K-Link Network settings', ['exception' => $qe]);
        }
    }
    
    /**
     * {@inherits}
     */
    public function canConnect($visibility = KlinkVisibilityType::KLINK_PRIVATE)
    {
        return false;
    }

    
    public static function test($url, $app_secret = null)
    {
        return false;
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


    /**
     * @param KlinkSearchRequest $searchRequest
     * @return KlinkSearchResults
     */
    public function search(KlinkSearchRequest $searchRequest)
    {
        KlinkVisibilityType::fromString($searchRequest->visibility()); // check if a valid visibility is used
        


        $this->calls['searching'] = array_merge($this->calls['searching'] ?? [], [$searchRequest]);

        return $this->search_results[$searchRequest->visibility()];
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

    public function setSearchResults($visibility, $results)
    {
        $this->search_results[$visibility] = $results;
        return $this;
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

    public static function generateFakeResults($count = 10)
    {
        $fakeResults = [];

        for ($i=0; $i < $count; $i++) {
            $fakeResults[] = tap(new Data(), function($data){

                $date = new \DateTime('2008-07-28T14:47:31Z', new \DateTimeZone('UTC'));
                
                $data->hash = self::$faker->sha256.self::$faker->sha256;
                $data->type = 'document';
                $data->url = self::$faker->url;
                $data->uuid = self::$faker->uuid;
        
                $author = new Author();
                $author->name = "An Author Name";
                $author->email = self::$faker->safeEmail;
        
                $data->author = [
                    $author
                ];
        
                $uploader = new Uploader();
                $uploader->name = "Uploader Name";
                $uploader->url = "http://some.profile/";
        
                $data->uploader = $uploader;
        
                $data->copyright = new Copyright();
                $data->copyright->owner = new CopyrightOwner();
                $data->copyright->owner->name = 'KLink Organization';
                $data->copyright->owner->email = 'info@klink.asia';
                $data->copyright->owner->contact = 'KLink Website: http://www.klink.asia';
        
                $data->copyright->usage = new CopyrightUsage();
                $data->copyright->usage->short = 'MPL-2.0';
                $data->copyright->usage->name = 'Mozilla Public License 2.0';
                $data->copyright->usage->reference = 'https://spdx.org/licenses/MPL-2.0.html';
        
                $data->properties = new Properties();
                $data->properties->title = self::$faker->sentence;
                $data->properties->filename = 'adventures-of-sherlock-holmes.pdf';
                $data->properties->mime_type = self::$faker->mimeType;
                $data->properties->language = 'en';
                $data->properties->created_at = $date;
                $data->properties->updated_at = $date;
                $data->properties->size = 150;
                $data->properties->abstract = self::$faker->sentence;
                $data->properties->thumbnail = self::$faker->url;
            });

             $fakeResultItem;
        }

        return $fakeResults;
    }

    public static function generateSearchResponse($terms, $visibility = KlinkVisibilityType::KLINK_PRIVATE, $resultsPerPage = 10, $page = 1, $facets = null)
    {
        if (! static::$faker) {
            static::$faker = FakerFactory::create();
        }

        $fakeResults = static::generateFakeResults($resultsPerPage);
        
        $searchRequest = KlinkSearchRequest::build($terms, $visibility, $page, $resultsPerPage, [], []);

        return KlinkSearchResults::fake($searchRequest, $fakeResults, static::generateFacetsResponse($facets, $visibility));
    }

    public static function generateFacetsResponse($facets, $visibility = KlinkVisibilityType::KLINK_PRIVATE)
    {
        if (! static::$faker) {
            static::$faker = FakerFactory::create();
        }

        if(empty($facets)){
            return [];
        }

        $aggregation_response = [];

        foreach ($facets as $facet) {
            $aggregation_response[$facet] = tap(new AggregationResult(), function($aggregation){
                $aggregation->value = static::$faker->name;
                $aggregation->count = static::$faker->randomDigit;
            });
        }

        return $aggregation_response;
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
        PHPUnit::assertTrue(isset($this->calls['indexing']), "KlinkAdapter addDocument or updateDocument not called");

        PHPUnit::assertNotEmpty($this->calls['indexing'], "KlinkAdapter addDocument or updateDocument not called");

        $filtered = array_filter($this->calls['indexing'], function ($el) use ($documentId) {
            return $el === $documentId;
        });

        PHPUnit::assertCount($times,

            $filtered,

            "The expected [{$documentId}] document was not indexed $times times."

        );
    }
    
    public function assertSearched(KlinkSearchRequest $request, $times = 1)
    {
        PHPUnit::assertNotEmpty($this->calls['searching'], "KlinkAdapter search not called");

        $filtered = array_filter($this->calls['searching'], function ($el) use ($request) {
            return $el->equals($request);
        });

        PHPUnit::assertCount($times,

            $filtered,

            "The expected search [{$request}] was not executed $times times."

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
