<?php

namespace KBox\Traits;

use BadMethodCallException;
use Illuminate\Http\Request;
use Klink\DmsSearch\SearchRequest;
use Illuminate\Support\Collection;
use Klink\DmsSearch\SearchService;
use Illuminate\Support\Facades\DB;
use KBox\Pagination\SearchResultsPaginator;
use KSearchClient\Model\Search\Search;

/**
 * Add support for faster access to search features
 */
trait Searchable
{
    /**
     * Execute a search based on the given SearchRequest.
     * In case the SearchRequest->isPageRequested() is equal to true the $override closure will be called (if not null)
     *
     * @param SearchRequest $request the search request
     * @param \Closure $override the callback that will be called if SearchRequest->isPageRequested() in order to search for local available Models, to the callback the SearchRequest object in $request is passed as first argument
     * @return SearchResultsPaginator the paginated results
     */
    public function search(SearchRequest $request, \Closure $override = null)
    {
        // You can return false in $override to tell the system to use a normal search invocation
        
        $override_response = (! is_null($override) && $override) ? $override($request) : false;
          
        if (! is_bool($override_response) && ! is_a($override_response, 'Illuminate\Support\Collection')
                && ! is_a($override_response, 'Illuminate\Database\Eloquent\Builder')
                && ! is_a($override_response, 'Illuminate\Database\Eloquent\Relations\Relation')) {
            throw new BadMethodCallException(sprintf('Bad $override callback return value. Expected boolean===false || Eloquent\Builder || Collection || Illuminate\Database\Eloquent\Relations\Relation, received "%s (parent: %s)"', get_class($override_response), get_parent_class($override_response)));
        }
          
        if ($override_response !== false && ! $request->isSearchRequested()) {
            $total = (method_exists($override_response, 'getCountForPagination')) ? $override_response->getCountForPagination() : $override_response->count();

            $to_highlight = $request->highlight;

            // someone wants to highlight a document?
  
            if (! is_null($to_highlight) && $to_highlight != 0) {
                // if collection, we already have all the results,
                // so we need to retrieve the element with ID == $to_highlight
                // and calculate its offset
  
                $new_page = $request->page;
  
                if (is_a($override_response, 'Illuminate\Support\Collection')) {
                    $key = $override_response->where('id', $to_highlight)->
                             keys()->first();
  
                    $new_page = floor($key / $request->limit) + 1;
                } else {
                    // duplicate the query to not change the original meaning
                    $row_count_query = clone $override_response;

                    // counting how many elements we have before the chosen one
                    // For this we use MariaDB session variables, because the counter
                    // is not available by default

                    DB::statement(DB::raw('set @row=0'));

                    $key = $row_count_query->select(DB::raw('@row:=@row+1 as row'), 'id')->get(['row', 'id'])
                          ->where('id', $to_highlight)->first()->row - 1; // row is base 1
                      
                    $new_page = floor($key / $request->limit) + 1;
                }
  
                // then edit requested page accordingly
                $request->page(intval($new_page, 10));
            }

            $paginated = $total === 0 ? new Collection() : (is_a($override_response, 'Illuminate\Support\Collection') ? $override_response->forPage($request->page, $request->limit)->values() : $override_response->forPage($request->page, $request->limit)->get());

            // merge th default facets with the current request
            $request->facets(SearchService::$defaultFacets[$request->visibility]);

            return new SearchResultsPaginator(
                $request->term === '*' ? '' : $request->term,
                $paginated,
                null,
                $this->facets($request),
                $total,
                $request->limit,
                $request->page,
                [
                        'path'  => $request->url,
                        'query' => collect($request->query)->except('highlight')->toArray(),
                    ]
            );
        } else {
            $core_results = app(SearchService::class)->search($request);
              
            return $core_results;
        }
    }
    
    /**
     * Retrieve the facets for subsequent filtering
     */
    public function facets(SearchRequest $request)
    {
        return app(SearchService::class)->aggregations($request);
    }
    
    public function searchRequestCreate(Request $request = null)
    {
        
        // need to find better way to implement this code
        $req = SearchRequest::create($request);

        // Number of Items per page 

        $items_per_page = (int)auth()->user()->optionItemsPerPage();

        $requested_items_per_page = (int)$request->input('n', $items_per_page);

        try {
            if ($items_per_page !== $requested_items_per_page) {
                auth()->user()->setOptionItemsPerPage($requested_items_per_page);
                $items_per_page = $requested_items_per_page;
            }
        } catch (\Exception $limit_ex) {
        }

       $req->limit($items_per_page);
        
        // end

        return $req;
    }
}
