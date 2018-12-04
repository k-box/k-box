<?php

namespace KBox\Http\Controllers\Document;

use Illuminate\Http\Request;
use KBox\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use KBox\Traits\Searchable;

class PublicDocumentsController extends Controller
{
    use Searchable;

    /*
    |--------------------------------------------------------------------------
    | Documents Controller
    |--------------------------------------------------------------------------
    |
    | Handle all the stuff related to document add, edit, remove,...
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
    public function __construct(\KBox\Documents\Services\DocumentsService $adapterService)
    {
        $this->middleware('auth');

        $this->middleware('capabilities');

        $this->service = $adapterService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(AuthGuard $auth, Request $request)
    {
        $user = $auth->user();

        $visibility = 'public';

        $req = $this->searchRequestCreate($request);
        
        $req->visibility($visibility);
        
        $results = $this->search($req, function ($_request) {
            
            // return direct search because we want them to see the public network
            return false;
        });

        return view('documents.documents', [
            'pagetitle' => network_name().' '.trans('documents.page_title'),
            'documents' => $results ? $results->getCollection() : collect(),
            'context' => is_null($visibility) ? 'all' : $visibility,
            'pagination' => $results,
            'is_search_failed' => $results === null,
            'search_terms' => $req->term,
            'facets' => $results !== null ? $results->facets() : [],
            'filters' => $results !== null ? $results->filters() : [],
            'current_visibility' => $visibility,
            'is_personal' => false,
            'hint' => false,
            'filter' => network_name()
            ]);
    }
}
