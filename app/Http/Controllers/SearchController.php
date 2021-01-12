<?php

namespace KBox\Http\Controllers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use KBox\Traits\Searchable;
use KBox\Exceptions\ForbiddenException;
use KBox\Option;
use Klink\DmsAdapter\KlinkVisibilityType;

class SearchController extends Controller
{
    use Searchable;

    /*
    |--------------------------------------------------------------------------
    | Search Controller
    |--------------------------------------------------------------------------
    |
    | This controller renders the "search results page".
    |
    */

    /**
     * [$adapter description]
     * @var \Klink\DmsAdapter\KlinkAdapter
     */
    private $service = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(\Klink\DmsSearch\SearchService $searchService)
    {
        $this->service = $searchService;
    }

    /**
     * Show the application welcome screen to the user.
     *
     * @return Response
     */
    public function index(Guard $auth, Request $request)
    {
        if (! $auth->check() && ! config('dms.are_guest_public_search_enabled')) {
            abort(403);
        }

        $is_klink_public_enabled = ! ! Option::option(Option::PUBLIC_CORE_ENABLED, false);

        if (! $is_klink_public_enabled) {
            throw new ForbiddenException('Public search disabled');
        }

        $req = $this->searchRequestCreate($request);
        
        $req->visibility(KlinkVisibilityType::KLINK_PUBLIC);
        
        $grand_total = $this->service->getTotalIndexedDocuments($req->visibility);

        $test = $all = $this->search($req);
        
        if ($request->wantsJson()) {
            if (! is_null($test)) {
                return response()->json($test);
            } else {
                return response('Error', 500);
            }
        }

        $result_facets = [];

        if (! is_null($test)) {
            $result_facets = $test->facets();
        }

        return view('search', [
            'classes' => 'page search',
            'pagetitle' => trans('search.page_title'),
            'search_error' => is_null($test),
            'search_terms' => $req->term,
            'results' => $test->items(),
            'total_results' => $test->total(),
            'pagination' => $test,
            'klink_indexed_documents_count' => $grand_total,
            'current_visibility' => $req->visibility,
            'filters' => $test->filters(),
            'filter' => network_name(),
            'facets' => $result_facets,
            'only_facets' => false,
            ]);
    }
}
