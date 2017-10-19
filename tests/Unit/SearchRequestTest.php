<?php

namespace Tests\Unit;

use Tests\TestCase;
use Klink\DmsAdapter\KlinkFacets;
use Klink\DmsAdapter\KlinkFilters;
use Klink\DmsSearch\SearchRequest;
use Klink\DmsSearch\SearchService;
use Klink\DmsAdapter\KlinkFacetsBuilder;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Request as BaseRequest;

class SearchRequestTest extends TestCase
{
    use DatabaseTransactions;

    private function createValidHttpRequest($params)
    {
        return HttpRequest::createFromBase(
            BaseRequest::create('http://search/', 'GET', $params)
        );
    }

    public function testSearchRequestUsesDefaultValues()
    {
        $req = SearchRequest::create();
        
        $this->assertInstanceOf('Klink\DmsSearch\SearchRequest', $req);
        $this->assertEquals('*', $req->term);
        $this->assertEquals(1, $req->page);
        $this->assertEquals(12, $req->limit);
        $this->assertEquals('private', $req->visibility);
        $this->assertNull($req->filters);
        $this->assertNull($req->facets);
        $this->assertNull($req->highlight);
        $this->assertEmpty($req->buildFilters());
        $this->assertEmpty($req->buildAggregations());
    }

    public function testSearchRequestBaseConstruction()
    {
        $req = SearchRequest::create()
            ->search('X') // term to search, default *
            ->page(2) //page number, the page to explore, default 1
            ->limit(12) // elements per page, default \Config::get('dms.items_per_page', 12);
            ->visibility('private'); // visibility: public or private -> if no visibility is specified defaults to 'private'

        $this->assertInstanceOf('Klink\DmsSearch\SearchRequest', $req);
        $this->assertEquals('X', $req->term);
        $this->assertEquals(2, $req->page);
        $this->assertEquals(12, $req->limit);
        $this->assertEquals('private', $req->visibility);
        $this->assertNull($req->filters);
        $this->assertNull($req->facets);
        $this->assertNull($req->highlight);
        $this->assertEmpty($req->buildFilters());
        $this->assertEmpty($req->buildAggregations());
    }
    
    public function testSearchRequestRespectFacetsAndFilters()
    {
        $this->markTestSkipped(
            'Needs to be reimplemented.'
          );

        $req = SearchRequest::create()
            ->search('X') // term to search, default *
            ->on(['0-10', '0-1', '0-15']) // limit the search to specific documents (by id)
            ->in([1,2,3,4,5]) // limit the search to specific collections (by id)
            ->inProject([8,9])
            ->facets(SearchService::$defaultFacets['private']); // tells which facets we want back, the same supported by the KLinkFacetsBuilder

        $this->assertInstanceOf('Klink\DmsSearch\SearchRequest', $req);
        $this->assertNotNull($req->facets);
        $this->assertEquals(SearchService::$defaultFacets['private'], $req->facets);
        $this->assertNull($req->filters);
        $this->assertInstanceOf('Illuminate\Support\Collection', $req->in_documents);
        $this->assertEquals(5, $req->in_documents->count());
        $this->assertInstanceOf('Illuminate\Support\Collection', $req->in_projects);
        $this->assertEquals(2, $req->in_projects->count());
        $this->assertInstanceOf('Illuminate\Support\Collection', $req->on_collections);
        $this->assertEquals(3, $req->on_collections->count());
        
        $facets_built = $req->buildAggregations();
        $filters_built = $req->buildFilters();
        
        $this->assertArrayHasKey(KlinkFacets::COLLECTIONS, $facets_built);
        $this->assertArrayHasKey(KlinkFacets::PROJECTS, $facets_built);
        $this->assertArrayHasKey(KlinkFacets::LANGUAGE, $facets_built);
        $this->assertArrayHasKey(KlinkFacets::MIME_TYPE, $facets_built);

        $this->assertTrue(is_array($filters_built), 'SearchRequest built filters are not an array');
        $this->assertNotEmpty($filters_built);
        $this->assertArrayHasKey(KlinkFilters::COLLECTIONS, $filters_built);
        $this->assertArrayHasKey(KlinkFilters::PROJECTS, $filters_built);
        $this->assertArrayHasKey(KlinkFilters::UUID, $filters_built);

        $local_document_id = $filters_built[KlinkFilters::UUID];
        $document_groups = $filters_built[KlinkFilters::COLLECTIONS];
        $project_ids = $filters_built[KlinkFilters::PROJECTS];
        
        $this->assertNotEmpty($local_document_id);
        $this->assertEquals(['1', '2', '3', '4', '5'], $local_document_id);
        $this->assertNotEmpty($document_groups);
        $this->assertEquals(['0-10','0-1','0-15'], $document_groups);
        $this->assertNotEmpty($project_ids);
        $this->assertEquals(['8','9'], $project_ids);
        $this->assertNull($req->explicit_filters);
    }

    public function testSearchRequestInProjects()
    {
        $req = SearchRequest::create()
            ->search('X') // term to search, default *
            ->inProject([8,9]);

        $this->assertInstanceOf('Klink\DmsSearch\SearchRequest', $req);
        
        $facets_built = $req->buildAggregations();
        $filters_built = $req->buildFilters();

        $this->assertEmpty($facets_built);
        $this->assertArrayHasKey(KlinkFilters::PROJECTS, $filters_built);

        $project_ids = $filters_built[KlinkFilters::PROJECTS];
        
        $this->assertNotEmpty($project_ids);
        $this->assertEquals(['8','9'], $project_ids);
        $this->assertNull($req->explicit_filters);
    }
    
    public function testSearchRequestCanBeConstractedFromHttpRequest()
    {
        $this->markTestSkipped(
            'Needs to be reimplemented.'
          );
          
        $req = SearchRequest::create($this->createValidHttpRequest(
            [
                's' => 'X',
                'page' => '2',
                'visibility' => 'public',
                'fs' => implode(',', SearchService::$defaultFacets['private']),
                'language' => 'en,ru',
                'collections' => '0:10,1:11',
                'mime_type' => 'document'
            ]
        ));

        $this->assertInstanceOf('Klink\DmsSearch\SearchRequest', $req);
        $this->assertEquals('X', $req->term);
        $this->assertEquals(2, $req->page);
        $this->assertEquals(12, $req->limit);
        $this->assertEquals('public', $req->visibility);
        $this->assertNotNull($req->facets);
        $this->assertEquals(SearchService::$defaultFacets['private'], $req->facets);
        $this->assertNotNull($req->filters);
        
        $this->assertEquals([
            KlinkFilters::LANGUAGE => ['en','ru'],
            KlinkFilters::COLLECTIONS => ['0:10','1:11'],
            KlinkFilters::MIME_TYPE => ['document']], $req->filters);
        
        $this->assertNotNull($req->explicit_filters);
        
        $this->assertContains(KlinkFilters::LANGUAGE, $req->explicit_filters);
        $this->assertContains(KlinkFilters::COLLECTIONS, $req->explicit_filters);
        $this->assertContains(KlinkFilters::MIME_TYPE, $req->explicit_filters);
        
        $built = $req->buildAggregations();
        
        $this->assertNotEmpty($built);
        $this->assertCount(4, $built);
    }
    
    /**
     * test construction based from Request seeding and "by hand" modification
     */
    public function testSearchRequestMixedConstruction()
    {
        $req = SearchRequest::create($this->createValidHttpRequest(
            [
                's' => 'X',
                'page' => '2',
                'visibility' => 'public',
                'fs' => 'documentType',
            ]
        ));
        
        $req->facets(['language','institutionId','documentGroups']);
        
        $req->filters(['language' => ['en','ru'],
            'documentGroups' => ['0:10','1:11'],
            'documentType' => ['document']]);

        $this->assertInstanceOf('Klink\DmsSearch\SearchRequest', $req);
        
        $this->assertEquals('X', $req->term);
        
        $this->assertEquals(2, $req->page);
        
        $this->assertEquals(12, $req->limit);
        
        $this->assertEquals('public', $req->visibility);
        
        $this->assertNotNull($req->facets);
        
        $this->assertEquals(['documentType','language','institutionId','documentGroups'], $req->facets);
        
        $this->assertNotNull($req->filters);
        
        $this->assertEquals([
            'language' => ['en','ru'],
            'documentGroups' => ['0:10','1:11'],
            'documentType' => ['document']], $req->filters);
        
        
        $facets_built = $req->buildAggregations();
        
        $this->assertNotEmpty($facets_built);
        $this->assertCount(4, $facets_built);
    }
}
