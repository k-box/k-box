<?php 

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KlinkDMS\Capability;
use KlinkDMS\Starred;
use Laracasts\TestDummy\Factory;
use Illuminate\Support\Collection;
use Klink\DmsSearch\SearchRequest;
use Illuminate\Http\Request;
use KlinkDMS\Traits\Searchable;

class SearchTest extends TestCase {

	use Searchable;
    use DatabaseMigrations;
    use DatabaseTransactions;
	
	public function testSearchRequestBaseConstruction()
	{
		
		$req = SearchRequest::create();

		$this->assertInstanceOf('Klink\DmsSearch\SearchRequest', $req);
		
		$this->assertEquals('*', $req->term);
		
		$this->assertEquals(1, $req->page);
		
		$this->assertEquals(12, $req->limit);
		
		$this->assertEquals('private', $req->visibility);
		
		$this->assertNull($req->filters);
		
		$this->assertNull($req->facets);
		
		
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
		
		// var_dump($req);
		
		// search process
		// 1. get the parameters to invoke the adapter search
		// 2. request a count search to get the total number of results (to be used in pagination)
		// 3. perform the real search to get the results
		// 4. return everything that must be returned

		
	}
	
	public function testSearchRequestAdvancedConstruction()
	{
		
		$req = SearchRequest::create()
			->search('X') // term to search, default *
			// ->page(2) //page number, the page to explore, default 1
			// ->limit(12) // elements per page, default \Config::get('dms.items_per_page', 12);
			// ->visibility('private') // visibility: public or private -> if no visibility is specified defaults to 'private'
			->on(['0:10', '0:1', '0:15']) // limit the search to specific documents (by id)
			->in([1,2,3,4,5]) // limit the search to specific collections (by id)
			// 
			// ->filters(array(
			// 	'language' => ['en','ru'],
			// 	'documentGroups' => ['0:10','1:11'],
			// 	'documentType' => ['document']))
			->facets(['documentType','language','institutionId','documentGroups']); // tells which facets we want back, the same supported by the KLinkFacetsBuilder
			// 
			// // future -> when could be supported
			// ->created('date', ['start', 'end']) // creation date
			// ->updated('date', ['start', 'end']) // updated date
			// ->locations(); //location
			
		// SearchRequest::create($request); //create passing a request states that you would use the search parameters contained in the request as starting point


		$this->assertInstanceOf('Klink\DmsSearch\SearchRequest', $req);
		
		// $this->assertEquals('X', $req->term);
		// 
		// $this->assertEquals(2, $req->page);
		// 
		// $this->assertEquals(12, $req->limit);
		// 
		// $this->assertEquals('private', $req->visibility);
		// 
		$this->assertNotNull($req->facets);
		
		$this->assertEquals(array('documentType','language','institutionId','documentGroups'), $req->facets);
		
		$this->assertNull($req->filters);
		
		$this->assertInstanceOf('Illuminate\Support\Collection', $req->in_documents);
		
		$this->assertEquals(5, $req->in_documents->count());
		
		$this->assertInstanceOf('Illuminate\Support\Collection', $req->on_collections);
		
		$this->assertEquals(3, $req->on_collections->count());
		
		$facets_built = $req->facets_and_filters->build();
		
		$local_document_id = array_values(array_filter($facets_built, function($el){
			return $el->getName() === 'localDocumentId';
		}));
		
		$document_groups = array_values(array_filter($facets_built, function($el){
			return $el->getName() === 'documentGroups';
		}));
		
		
		$this->assertNotEmpty($local_document_id);
		
		$this->assertInstanceOf('\KlinkFacet', $local_document_id[0]);
		
		$this->assertEquals('1,2,3,4,5', $local_document_id[0]->getFilter());
		
		$this->assertNotEmpty($document_groups);
		
		$this->assertInstanceOf('\KlinkFacet', $document_groups[0]);
		
		$this->assertEquals('0:10,0:1,0:15', $document_groups[0]->getFilter());
		
		
		
		// $this->assertEquals(array(
		// 	'language' => ['en','ru'],
		// 	'documentGroups' => ['0:10','1:11'],
		// 	'documentType' => ['document']), $req->filters);
		
		// var_dump($req);
		
		// search process
		// 1. get the parameters to invoke the adapter search
		// 2. request a count search to get the total number of results (to be used in pagination)
		// 3. perform the real search to get the results
		// 4. return everything that must be returned

		
	}
	
	public function testSearchRequestFromRequestConstruction()
	{

		$http_request = Request::createFromBase(
			Symfony\Component\HttpFoundation\Request::create(
				'http://search/', 'GET',
				array(
					's' => 'X',
					'page' => '2',
					'visibility' => 'public',
					'fs' => 'documentType,language,institutionId,documentGroups',
					'language' => 'en,ru',
					'documentGroups' => '0:10,1:11',
					'documentType' => 'document'
				))
				//?s=X&page=2&visibility=public&fs=documentType&documentType=document...
		);
		
		$req = SearchRequest::create($http_request);
		
		// var_dump($req);

		$this->assertInstanceOf('Klink\DmsSearch\SearchRequest', $req);
		
		$this->assertEquals('X', $req->term);
		
		$this->assertEquals(2, $req->page);
		
		$this->assertEquals(12, $req->limit);
		
		$this->assertEquals('public', $req->visibility);
		
		$this->assertNotNull($req->facets);
		
		$this->assertEquals(array('documentType','language','institutionId','documentGroups'), $req->facets);
		
		$this->assertNotNull($req->filters);
		
		$this->assertEquals(array(
			'language' => ['en','ru'],
			'documentGroups' => ['0:10','1:11'],
			'documentType' => ['document']), $req->filters);
		
		$fs_and_fs = $req->facets_and_filters;
		
		$this->assertInstanceOf('\KlinkFacetsBuilder', $fs_and_fs);
		
		$built = $fs_and_fs->build();
		
		$this->assertNotEmpty($built);

		$this->assertCount(4, $built);
		
	}
	
	/** 
	 * test construction based from Request seeding and "by hand" modification
	 */
	public function testSearchRequestMixedConstruction()
	{
		
		$http_request = Request::createFromBase(
			Symfony\Component\HttpFoundation\Request::create(
				'http://search/', 'GET',
				array(
					's' => 'X',
					'page' => '2',
					'visibility' => 'public',
					'fs' => 'documentType',
					
				))
				//?s=X&page=2&visibility=public&fs=documentType&documentType=document...
		);
		
		$req = SearchRequest::create($http_request);
		
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
		
		$this->assertEquals(array('documentType','language','institutionId','documentGroups'), $req->facets);
		
		$this->assertNotNull($req->filters);
		
		$this->assertEquals(array(
			'language' => ['en','ru'],
			'documentGroups' => ['0:10','1:11'],
			'documentType' => ['document']), $req->filters);
		
		$fs_and_fs = $req->facets_and_filters;
		
		$this->assertInstanceOf('\KlinkFacetsBuilder', $fs_and_fs);
		
		$built = $fs_and_fs->build();
		
		$this->assertNotEmpty($built);

		$this->assertCount(4, $built);

		
	}


	public function testSearchAction(){
		
		$http_request = Request::createFromBase(
			Symfony\Component\HttpFoundation\Request::create(
				'http://search/', 'GET',
				array(
					's' => '*',
					'page' => '1',
					'visibility' => 'private'
					
				))
				//?s=X&page=2&visibility=public&fs=documentType&documentType=document...
		);
		
		$req = SearchRequest::create($http_request);
		
		$search_service = app('Klink\DmsSearch\SearchService');
		
		$expected_total_results = $search_service->getTotalIndexedDocuments('private', true);
		
		$res = $search_service->search($req);
		
		$this->assertInstanceOf('KlinkDMS\Pagination\SearchResultsPaginator', $res, 'Result not a paginator');
		
		$this->assertNotEmpty($res->items());
		$this->assertNotNull($res->items());
		$this->assertInstanceOf('Illuminate\Support\Collection', $res->getCollection(), 'Result items as collection');
		
		$this->assertNotNull($res->facets());
		
		$this->assertEquals($req->limit, $res->count(), 'Document count == requested limit');
		$this->assertEquals($req->limit, $res->perPage(), 'Limit count');
		
		// total(), lastPage(), toArray(), toJson(), items(), currentPage(), perPage()
		
		$this->assertEquals($req->page, $res->currentPage());
		$this->assertEquals($expected_total_results, $res->total(), 'Total results');
		
		$total_pages = $res->lastPage();
		for($pg = $res->currentPage() + 1; $pg < $total_pages; $pg++){
			
			var_dump(sprintf('Requesting page %s of %s', $pg, $total_pages));
			
			$req->page($pg);
			
			$res = $search_service->search($req);
			
			$this->assertEquals($req->page, $res->currentPage(), 'Next pages - current page');
			
			$this->assertInstanceOf('KlinkDMS\Pagination\SearchResultsPaginator', $res, 'Result not a paginator');
		
			$this->assertNotEmpty($res->items());
			$this->assertNotNull($res->items());
			
		}
		
	}
	
	
	public function testSearchStarred_all_override(){
        
        // add some documents and star them
        
        $user = $this->createAdminUser();
        
        $starred = factory('KlinkDMS\DocumentDescriptor', 3)
            ->create()
            ->each(function($doc) use ($user) {
                $doc->stars()->create(['user_id' => $user->id]);
            });
        
		$expected_total_results = Starred::with('document')->ofUser($user->id)->count();
		
		$starred_docs_ids = Starred::with('document')->ofUser($user->id)->get()->fetch('document.local_document_id')->all();
		
		$req = SearchRequest::create()->page(1)->limit(1);
		
		$this->assertTrue($req->isAllRequested(), 'isAllRequested');
		$this->assertTrue($req->isPageRequested(), 'isPageRequested');
		$this->assertFalse($req->isSearchRequested(), 'isSearchRequested');
		
		$that = $this;
		
		$results = $this->search($req, function($_request) use($that, $user){
			
			$that->assertInstanceOf('Klink\DmsSearch\SearchRequest', $_request);
			
			return Starred::with('document')->ofUser($user->id); // or Collection or Eloquent\Builder instance
		});
		
		$this->assertInstanceOf('KlinkDMS\Pagination\SearchResultsPaginator', $results, 'Result not a paginator');
		
		$this->assertNotEmpty($results->items());
		$this->assertNotNull($results->items());
		$this->assertInstanceOf('Illuminate\Support\Collection', $results->getCollection(), 'Result items as collection');
		
		$this->assertEquals($req->limit, $results->count(), 'Document count == requested limit');
		$this->assertEquals($req->limit, $results->perPage(), 'Limit count');
		
		// total(), lastPage(), toArray(), toJson(), items(), currentPage(), perPage()
		
		$this->assertEquals($req->page, $results->currentPage());
		$this->assertEquals($expected_total_results, $results->total(), 'Total results');
		
		$this->assertNotNull($results->facets(), 'Null Facets, where the default facets are?');
	}
	
	public function testSearchStarred_in(){
		
        $this->markTestIncomplete(
          'This test Requires that the documents are indexed in the core.'
        );
        
        $user = $this->createAdminUser();
        
        $starred = factory('KlinkDMS\DocumentDescriptor', 3)
            ->create()
            ->each(function($doc) use ($user) {
                $doc->stars()->create(['user_id' => $user->id]);
            });
        
		$expected_total_results = Starred::with('document')->ofUser($user->id)->count();
		
		$starred_docs_ids = Starred::with('document')->ofUser($user->id)->get()->fetch('document.local_document_id')->all();
		
		$req = SearchRequest::create()->page(1)->limit($expected_total_results);
		
		$this->assertTrue($req->isAllRequested(), '1 - isAllRequested');
		$this->assertTrue($req->isPageRequested(), '1 - isPageRequested');
		$this->assertFalse($req->isSearchRequested(), '1 - isSearchRequested');
		
		$that = $this;
		
		$results = $this->search($req, function($_request) use($that, $starred_docs_ids){
			
			$that->assertInstanceOf('Klink\DmsSearch\SearchRequest', $_request);
			
			$_request->in($starred_docs_ids);
			
			return false; // force to execute a search on the core instead on the database
		});
		
		$this->assertInstanceOf('KlinkDMS\Pagination\SearchResultsPaginator', $results, 'Result not a paginator');
		
		$this->assertNotEmpty($results->items());
		$this->assertNotNull($results->items());
		$this->assertInstanceOf('Illuminate\Support\Collection', $results->getCollection(), 'Result items as collection');
		
		$this->assertEquals($req->limit, $results->count(), 'Document count == requested limit');
		$this->assertEquals($req->limit, $results->perPage(), 'Limit count');
		
		// total(), lastPage(), toArray(), toJson(), items(), currentPage(), perPage()
		
		$this->assertEquals($req->page, $results->currentPage());
		$this->assertEquals($expected_total_results, $results->total(), 'Total results');
		$this->assertEquals($expected_total_results, $results->getCollection()->count(), 'Total results');
		
		$this->assertNotNull($results->facets(), 'Null Facets, where the default facets are?');
		
		
		
		// // questa dovrebbe essere una ricerca vera e propria o no?
		// $results = $this->search($req);
		// 
		// 
		// 
		// $req = SearchRequest::create()->search('X')->page(1)->limit(1)->in([1,2]);
		// $this->assertFalse($req->isAllRequested(), '3 - isAllRequested');
		// $this->assertFalse($req->isPageRequested(), '3 - isPageRequested');
		// $this->assertTrue($req->isSearchRequested(), '3 - isSearchRequested');
		// // questa dovrebbe essere una ricerca vera e propria?
		// $results = $this->search($req);
		// 
		// create test document set, mark them as starred
		// search for known documents
		// check if are returned
	}

// 		
// 		$this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
// 		
// 		$this->assertEquals(array('status' => 'ok'), $response->getData(true));
// 		
// 		$this->assertEquals(0, Starred::count());
// 	}

}