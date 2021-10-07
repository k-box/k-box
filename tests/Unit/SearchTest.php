<?php

namespace Tests\Unit;

use KBox\Starred;
use Tests\TestCase;
use KBox\Traits\Searchable;
use KBox\DocumentDescriptor;
use Klink\DmsSearch\SearchRequest;
use Illuminate\Http\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Request as BaseRequest;
use Illuminate\Support\Collection;
use KBox\Pagination\SearchResultsPaginator;
use Klink\DmsAdapter\Fakes\FakeKlinkAdapter;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Capability;
use KBox\Sorter;
use KBox\User;

class SearchTest extends TestCase
{
    use Searchable;
    use DatabaseTransactions;
    
    public function testSearchStarred_all_override()
    {
        $this->withKlinkAdapterFake();

        // add some documents and star them
        
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });
        
        $starred = factory(DocumentDescriptor::class, 3)
            ->create()
            ->each(function ($doc) use ($user) {
                $doc->stars()->create(['user_id' => $user->id]);
            });
        
        $expected_total_results = Starred::with('document')->ofUser($user->id)->count();
        
        $starred_docs_ids = Starred::with('document')->ofUser($user->id)->get()->pluck('document.local_document_id')->all();
        
        $req = SearchRequest::create()->page(1)->limit(1);
        
        $this->assertTrue($req->isAllRequested(), 'isAllRequested');
        $this->assertTrue($req->isPageRequested(), 'isPageRequested');
        $this->assertFalse($req->isSearchRequested(), 'isSearchRequested');
        
        $that = $this;
        
        $results = $this->search($req, function ($_request) use ($that, $user) {
            $that->assertInstanceOf(SearchRequest::class, $_request);
            
            return Starred::with('document')->ofUser($user->id); // or Collection or Eloquent\Builder instance
        });
        
        $this->assertInstanceOf(SearchResultsPaginator::class, $results, 'Result not a paginator');
        
        $this->assertNotEmpty($results->items());
        $this->assertNotNull($results->items());
        $this->assertInstanceOf(Collection::class, $results->getCollection(), 'Result items as collection');
        
        $this->assertEquals($req->limit, $results->count(), 'Document count == requested limit');
        $this->assertEquals($req->limit, $results->perPage(), 'Limit count');
        
        // total(), lastPage(), toArray(), toJson(), items(), currentPage(), perPage()
        
        $this->assertEquals($req->page, $results->currentPage());
        $this->assertEquals($expected_total_results, $results->total(), 'Total results');
        
        $this->assertNotNull($results->facets(), 'Null Facets, where the default facets are?');
    }

    public function highlight_provider()
    {
        return [
            // first: how many documents to generate
            // second: the expected page
            // third: the expected position in the page
            // elements per page: 12
            [ 1, 1, 0 ],
            [ 2, 1, 1 ],
            [ 10, 1, 9 ],
            [ 11, 1, 10 ],
            [ 12, 1, 11 ],
            [ 13, 2, 0 ],
            [ 24, 2, 11 ],
            [ 25, 3, 0 ],
        ];
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

        $mock->shouldReceive('institutions')->never();
        
        $mock->shouldReceive('isNetworkEnabled')->never();

        $mock->shouldReceive('facets')->andReturnUsing(function ($facets, $visibility, $term = '*') {
            return FakeKlinkAdapter::generateFacetsResponse($facets, $visibility, $term);
        });

        $docs = factory(DocumentDescriptor::class, $count)->create();

        $interested_in = $docs->last();
        
        $req = SearchRequest::create()
            ->highlight($interested_in->id);

        $this->assertInstanceOf(SearchRequest::class, $req);
        
        $this->assertEquals($interested_in->id, $req->highlight);
        
        // Now let's consider a search over all the documents available to show
        // the page that contains the highlighted one

        $results = $this->search($req, function ($_request) use ($docs, $count) {
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

        $results = $this->search($req, function ($_request) use ($docs) {
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
            $created = factory(DocumentDescriptor::class)->create([
                'title' => $title
            ]);

            if ($index===0) {
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

        $this->assertInstanceOf(SearchRequest::class, $req);
        
        $this->assertEquals($interested_in->id, $req->highlight);
        $this->assertEquals($per_page, $req->limit);
        
        // Now let's consider a search over all the documents available to show
        // the page that contains the highlighted one

        $results = $this->search($req, function ($_request) use ($docs, $count) {
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

        $requestForSorting = HttpRequest::createFromBase(
            BaseRequest::create('http://search/', 'GET', [
                'sc' => 'name',
                'o' => 'a',
            ])
        );

        $reqQuery = SearchRequest::create()
            ->limit($per_page)
            ->setSorter(Sorter::fromRequest($requestForSorting))
            ->highlight($interested_in->id);
        
        $docsQuery = DocumentDescriptor::where('id', '>=', $first_element)->
                   where('id', '<=', $last_element)->orderBy('title', 'asc');

        $resultsUsingQuery = $this->search($reqQuery, function ($_request) use ($docsQuery) {
            return $docsQuery;
        });

        $this->assertEquals($expected_page, $resultsUsingQuery->currentPage());
        
        $this->assertEquals($per_page, $resultsUsingQuery->perPage());

        $this->assertEquals($count, $resultsUsingQuery->total());
        
        $this->assertTrue($resultsUsingQuery->hasMorePages());

        $firstUsingQuery = $resultsUsingQuery[$expected_position_in_page];

        $this->assertEquals($interested_in->id, $firstUsingQuery->id);
    }
}
