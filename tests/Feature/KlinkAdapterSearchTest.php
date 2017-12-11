<?php

namespace Tests\Feature;

use Tests\TestCase;
use KSearchClient\Model\Data\Data;
use Klink\DmsAdapter\KlinkDocument;
use Klink\DmsAdapter\KlinkSearchResults;
use Klink\DmsAdapter\KlinkSearchRequest;
use Klink\DmsAdapter\KlinkSearchResultItem;
use Klink\DmsAdapter\KlinkFacets;
use Klink\DmsAdapter\KlinkFilters;
use KSearchClient\Model\Data\AggregationResult;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class KlinkAdapterSearchTest extends TestCase
{
    use DatabaseTransactions;

    private $adapter = null;

    private $indexedDataUUIDs = null;

    protected function setUp()
    {
        parent::setUp();

        if (empty(getenv('DMS_CORE_ADDRESS'))) {
            $this->markTestSkipped(
              'DMS_CORE_ADDRESS not configured for running integration tests.'
            );
        }

        // generate and index some data
        $this->adapter = app('klinkadapter');

        $this->indexedDataUUIDs = collect();

        $descriptors = factory('KBox\DocumentDescriptor', 5)->create();
        
        $descriptors->each(function ($descriptor) {
            $response = $this->adapter->addDocument(
                new KlinkDocument($descriptor->toKlinkDocumentDescriptor(), 'test file content'));

            $this->indexedDataUUIDs->push($descriptor->uuid);
        });
    }

    protected function tearDown()
    {
        if ($this->adapter && ! empty($this->indexedDataUUIDs)) {
            $this->indexedDataUUIDs->each(function ($uuid) {
                $this->adapter->removeDocumentById($uuid, 'private');
            });
        }
    }

    public function test_search_retrieves_results()
    {
        $klink_search_request = KlinkSearchRequest::build('*', 'private', 1, 10, [
            KlinkFacets::LANGUAGE
        ]);

        $response = $this->adapter->search($klink_search_request);

        $this->assertInstanceOf(KlinkSearchResults::class, $response);

        $this->assertEquals('*', $response->getTerms());
        $this->assertEquals('private', $response->getVisibility());
        $this->assertEquals(10, $response->getResultsPerPage());
        $this->assertEquals(0, $response->getOffset());
        $this->assertEquals($this->indexedDataUUIDs->count(), $response->getTotalResults());
        $this->assertTrue($response->getSearchTime() >= 0);
        $this->assertEquals($this->indexedDataUUIDs->count(), $response->getCurrentResultCount());
        $this->assertArrayHasKey('properties.language', $response->getFacets());
        $this->assertContainsOnlyInstancesOf(KlinkSearchResultItem::class, $response->getResults());
    }

    public function test_facets_retrieves_results()
    {
        $response = $this->adapter->facets([
            KlinkFacets::LANGUAGE
        ], 'private');

        $this->assertTrue(is_array($response));
        $this->assertArrayHasKey('properties.language', $response);
        $this->assertNotEmpty($response['properties.language']);
        $this->assertContainsOnlyInstancesOf(AggregationResult::class, $response['properties.language']);
    }

    public function test_search_retrieves_filtered_results()
    {
        $uuids = $this->indexedDataUUIDs->take(2);

        $klink_search_request = KlinkSearchRequest::build('*', 'private', 1, 10, [
            KlinkFacets::LANGUAGE
        ], [
            KlinkFilters::UUID => $uuids->toArray()
        ]);

        $response = $this->adapter->search($klink_search_request);

        $this->assertInstanceOf(KlinkSearchResults::class, $response);

        $this->assertEquals('*', $response->getTerms());
        $this->assertEquals('private', $response->getVisibility());
        $this->assertEquals(10, $response->getResultsPerPage());
        $this->assertEquals(0, $response->getOffset());
        $this->assertEquals($uuids->count(), $response->getTotalResults());
        $this->assertTrue($response->getSearchTime() >= 0);
        $this->assertEquals($uuids->count(), $response->getCurrentResultCount());
        // $this->assertEmpty($response->getFacets());
        $this->assertContainsOnlyInstancesOf(KlinkSearchResultItem::class, $response->getResults());
    }
}
