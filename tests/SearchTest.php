<?php 

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KlinkDMS\Capability;
use KlinkDMS\Starred;
use KlinkDMS\DocumentDescriptor;
use Laracasts\TestDummy\Factory;
use Illuminate\Support\Collection;
use Klink\DmsSearch\SearchRequest;
use Illuminate\Http\Request;
use KlinkDMS\Traits\Searchable;

use Klink\DmsAdapter\Fakes\FakeKlinkAdapter;

class SearchTest extends TestCase {

	use Searchable;
    // use DatabaseMigrations;
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
		$this->assertNull($req->highlight);
		
		
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
			->inProject([8,9])
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
		
		$this->assertNotNull($req->facets);
		
		$this->assertEquals(array('documentType','language','institutionId','documentGroups'), $req->facets);
		
		$this->assertNull($req->filters);
		
		$this->assertInstanceOf('Illuminate\Support\Collection', $req->in_documents);
		
		$this->assertEquals(5, $req->in_documents->count());
		
		$this->assertInstanceOf('Illuminate\Support\Collection', $req->in_projects);
		
		$this->assertEquals(2, $req->in_projects->count());
		
		$this->assertInstanceOf('Illuminate\Support\Collection', $req->on_collections);
		
		$this->assertEquals(3, $req->on_collections->count());
		
		$facets_built = $req->facets_and_filters->build();
		
		$local_document_id = array_values(array_filter($facets_built, function($el){
			return $el->getName() === 'localDocumentId';
		}));
		
		$document_groups = array_values(array_filter($facets_built, function($el){
			return $el->getName() === 'documentGroups';
		}));

		$project_ids = array_values(array_filter($facets_built, function($el){
			return $el->getName() === 'projectId';
		}));
		
		
		$this->assertNotEmpty($local_document_id);
		
		$this->assertInstanceOf('\KlinkFacet', $local_document_id[0]);
		
		$this->assertEquals('1,2,3,4,5', $local_document_id[0]->getFilter());
		
		$this->assertNotEmpty($document_groups);
		
		$this->assertInstanceOf('\KlinkFacet', $document_groups[0]);
		
		$this->assertEquals('0:10,0:1,0:15', $document_groups[0]->getFilter());

		$this->assertNotEmpty($project_ids);
		
		$this->assertInstanceOf('\KlinkFacet', $project_ids[0]);
		
		$this->assertEquals('8,9', $project_ids[0]->getFilter());

		$this->assertNull($req->explicit_filters);
		
		
		
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

	public function testSearchRequestInProjects()
	{
		
		$req = SearchRequest::create()
			->search('X') // term to search, default *
			->inProject([8,9]);

		$this->assertInstanceOf('Klink\DmsSearch\SearchRequest', $req);
		
		$facets_built = $req->facets_and_filters->build();

		$project_ids = array_values(array_filter($facets_built, function($el){
			return $el->getName() === 'projectId';
		}));
		

		$this->assertNotEmpty($project_ids);
		
		$this->assertInstanceOf('\KlinkFacet', $project_ids[0]);
		
		$this->assertEquals('8,9', $project_ids[0]->getFilter());

		$this->assertNull($req->explicit_filters);
		
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
		
		$this->assertNotNull($req->explicit_filters);

		$this->assertEquals(['documentType', 'language', 'documentGroups'], $req->explicit_filters);

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

		$this->withKlinkAdapterFake();
		
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
			
			// var_dump(sprintf('Requesting page %s of %s', $pg, $total_pages));
			$req = $req->page($pg);
			
			$res = $search_service->search($req);
			
			
			$this->assertEquals($req->page, $res->currentPage(), 'Next pages - current page');
			
			$this->assertInstanceOf('KlinkDMS\Pagination\SearchResultsPaginator', $res, 'Result not a paginator');
		
			$this->assertNotEmpty($res->items());
			$this->assertNotNull($res->items());
			
		}
		
	}
	
	
	public function testSearchStarred_all_override(){

		$this->withKlinkAdapterFake();

        // add some documents and star them
        
        $user = $this->createAdminUser();
        
        $starred = factory('KlinkDMS\DocumentDescriptor', 3)
            ->create()
            ->each(function($doc) use ($user) {
                $doc->stars()->create(['user_id' => $user->id]);
            });
        
		$expected_total_results = Starred::with('document')->ofUser($user->id)->count();
		
		$starred_docs_ids = Starred::with('document')->ofUser($user->id)->get()->pluck('document.local_document_id')->all();
		
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
		
		$starred_docs_ids = Starred::with('document')->ofUser($user->id)->get()->pluck('document.local_document_id')->all();
		
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
		
		
	}


	public function highlight_provider(){
		
		return array( 
			// first: how many documents to generate
			// second: the expected page
			// third: the expected position in the page
			// elements per page: 12
			array( 1, 1, 0 ),
			array( 2, 1, 1 ),
			array( 10, 1, 9 ),
			array( 11, 1, 10 ),
			array( 12, 1, 11 ),
			array( 13, 2, 0 ),
			array( 24, 2, 11 ),
			array( 25, 3, 0 ),
		);
        
	}

	/**
	 * Test that the highlight attribute work as expected
	 *
	 * @param $count how many documents to create
	 * @param $expected_page The page on which we expect to be after the highlight
	 * @param $expected_position_in_page The expected offset in the page result of our highlighted element
	 *
	 * @dataProvider highlight_provider
	 */
	public function testSearchRequestWithHighlight($count, $expected_page, $expected_position_in_page)
	{

		$mock = $this->withKlinkAdapterMock();

		$mock->shouldReceive('institutions')->andReturn(factory('KlinkDMS\Institution')->make());
        
        $mock->shouldReceive('isNetworkEnabled')->andReturn(false);

		$mock->shouldReceive('facets')->andReturnUsing(function($facets, $visibility, $term = '*'){
			
            return FakeKlinkAdapter::generateFacetsResponse($facets, $visibility, $term);

        });


		$generated = factory('KlinkDMS\DocumentDescriptor', $count)->create();

		$docs = $count === 1 ? collect([$generated]) : $generated ;
        
		// $mock->shouldReceive('search')->andReturnUsing(function($terms, $type, $resultsPerPage, $offset, $facets) use($docs, $count){
		// 	dump(func_get_args());
        //     $res = FakeKlinkAdapter::generateSearchResponse($terms, $type, $resultsPerPage, $offset, $facets);


		// 	if($count === 1)
		// 	{
		// 		$res->items = [$docs->toKlinkDocumentDescriptor()];

		// 	}
		// 	else 
		// 	{
		// 		$res->items = $docs->map(function($i){
		// 			return $i->toKlinkDocumentDescriptor();
		// 		})->toArray();

		// 	}


        //     return $res;

        // });

		$interested_in = $docs->last();
		
		$req = SearchRequest::create()
			->highlight($interested_in->id);


		$this->assertInstanceOf('Klink\DmsSearch\SearchRequest', $req);
		
		$this->assertEquals($interested_in->id, $req->highlight);
		
		// Now let's consider a search over all the documents available to show
		// the page that contains the highlighted one 

		$results = $this->search($req, function($_request) use($docs, $count){
			
			return $docs; // the general collection that contains the documents
		});

		// Here the page number must be different than 1 and equal to 3
		$this->assertEquals($expected_page, $results->currentPage());
		
		$this->assertEquals(12, $results->perPage());

		$this->assertEquals($count, $results->total());
		
		$this->assertFalse($results->hasMorePages());

		$first = $results[$expected_position_in_page];
		
		$this->assertEquals($interested_in->id, $first->id);

		

		// Now let's use a query builder instance instead of a Collection
		$req = SearchRequest::create()
			->highlight($interested_in->id);

		$results = $this->search($req, function($_request) use($docs){
			
			return DocumentDescriptor::where('id', '>=', $docs->first()->id)->
			       where('id', '<=', $docs->last()->id);
		});

		// Here the page number must be different than 1 and equal to 3
		$this->assertEquals($expected_page, $results->currentPage());
		
		$this->assertEquals(12, $results->perPage());

		$this->assertEquals($count, $results->total());
		
		$this->assertFalse($results->hasMorePages());

		$first = $results[$expected_position_in_page];

		$this->assertEquals($interested_in->id, $first->id);
		
	}

	/**
	 * Test that the highlight attribute work as expected when ordering clause are applied
	 */
	public function testSearchRequestWithHighlightAndCustomOrderClause()
	{

		$this->withKlinkAdapterFake();

		$document_names = ['a', 'z', 'b', 'c', 'm', 'k'];
		$ordered_document_names = ['a', 'b', 'c', 'k', 'm', 'z' ];
		$count = count($document_names);
		$per_page = 2;
		$interested_in_title = 'k';
		$expected_page = 2;
		$expected_position_in_page = 1;

		$first_element = null;
		$last_element = null;

		foreach ($document_names as $index => $title) {

			$created = factory('KlinkDMS\DocumentDescriptor')->create([
				'title' => $title
			]);

			if($index===0){
				$first_element = $created->id;
			}

			$last_element = $created->id;

		}


		$docs = DocumentDescriptor::where('id', '>=', $first_element)->
			       where('id', '<=', $last_element)->orderBy('title', 'asc');

	    $interesting_query = clone $docs;
		$interested_in = $interesting_query->where('title', $interested_in_title)->first();
		
		$req = SearchRequest::create()
		    ->limit($per_page)
			->highlight($interested_in->id);


		$this->assertInstanceOf('Klink\DmsSearch\SearchRequest', $req);
		
		$this->assertEquals($interested_in->id, $req->highlight);
		$this->assertEquals($per_page, $req->limit);
		
		// Now let's consider a search over all the documents available to show
		// the page that contains the highlighted one 

		$results = $this->search($req, function($_request) use($docs, $count){
			
			return $docs->get(); // the general collection that contains the documents

		});

		// Here the page number must be different than 1 and equal to 3
		$this->assertEquals($expected_page, $results->currentPage());
		
		$this->assertEquals($per_page, $results->perPage());

		$this->assertEquals($count, $results->total());
		
		$this->assertTrue($results->hasMorePages());

		$first = $results[$expected_position_in_page];

		$this->assertEquals($interested_in->id, $first->id);

		

		// Now let's use a query builder instance instead of a Collection
		$req = SearchRequest::create()
		    ->limit($per_page)
			->highlight($interested_in->id);
		
		$docs = DocumentDescriptor::where('id', '>=', $first_element)->
			       where('id', '<=', $last_element)->orderBy('title', 'asc');

		$results = $this->search($req, function($_request) use($docs){
			
			return $docs;
		});

		$this->assertEquals($expected_page, $results->currentPage());
		
		$this->assertEquals($per_page, $results->perPage());

		$this->assertEquals($count, $results->total());
		
		$this->assertTrue($results->hasMorePages());

		$first = $results[$expected_position_in_page];

		$this->assertEquals($interested_in->id, $first->id);
		
	}

}