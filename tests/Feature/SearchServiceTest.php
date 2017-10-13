<?php

namespace Tests\Feature;

use Tests\TestCase;
use Klink\DmsSearch\SearchRequest;
use Klink\DmsAdapter\KlinkSearchResults;
use Klink\DmsAdapter\KlinkSearchRequest;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Request as BaseRequest;

class SearchServiceTest extends TestCase
{
    use DatabaseTransactions;
    
    private function createValidHttpRequest($params)
    {
        return HttpRequest::createFromBase(
            BaseRequest::create('http://search/', 'GET', $params)
        );
    }
    
    public function testSearchServiceReturnsPaginatedDocumentDescriptors()
    {
        $adapter = $this->withKlinkAdapterFake();

        // prepare the request
        $searchRequest = KlinkSearchRequest::build('*', 'private', 1, 1, [], []);
        
        // prepare some fake results
        $adapter->setSearchResults('private', KlinkSearchResults::fake($searchRequest, $adapter::generateFakeResults(24)));
        
        $req = SearchRequest::create($this->createValidHttpRequest(
            [
                's' => '*',
                'page' => '1',
                'visibility' => 'private',
            ]
        ));
        
        $search_service = app('Klink\DmsSearch\SearchService');
        
        $expected_total_results = $search_service->getTotalIndexedDocuments('private', true);
        
        $res = $search_service->search($req);
        
        $this->assertInstanceOf('KlinkDMS\Pagination\SearchResultsPaginator', $res, 'Result not a paginator');

        $this->assertNotEmpty($res->items(), 'search result is empty');
        $this->assertInstanceOf('Illuminate\Support\Collection', $res->getCollection(), 'Result items as collection');
        
        $this->assertNotNull($res->facets());
        
        $this->assertTrue($res->count() >= $req->limit, 'Document count < requested limit');
        $this->assertEquals($req->limit, $res->perPage(), 'Limit count');
        
        $this->assertEquals($req->page, $res->currentPage());
        $this->assertEquals($expected_total_results, $res->total(), 'Total results');
        
        $total_pages = $res->lastPage();
        for ($pg = $res->currentPage() + 1; $pg < $total_pages; $pg++) {
            $req = $req->page($pg);
            
            $res = $search_service->search($req);
            
            $this->assertEquals($req->page, $res->currentPage(), 'Next pages - current page');
            
            $this->assertInstanceOf('KlinkDMS\Pagination\SearchResultsPaginator', $res, 'Result not a paginator');
        
            $this->assertNotEmpty($res->items());
            $this->assertNotNull($res->items());
        }
    }
}
