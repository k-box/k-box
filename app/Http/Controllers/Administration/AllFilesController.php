<?php

namespace KBox\Http\Controllers\Administration;

use Illuminate\Http\Request;
use KBox\Http\Controllers\Controller;
use KBox\DocumentDescriptor;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use Illuminate\Support\Facades\Gate;
use KBox\Traits\Searchable;
use Klink\DmsAdapter\KlinkVisibilityType;

class AllFilesController extends Controller
{
    use Searchable;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(AuthGuard $auth, Request $request, $visibility = 'private')
    {
        Gate::authorize('manage-kbox');
        
        $user = $auth->user();

        if (! $user->isDMSManager()) {
            abort(403);
        }
        
        $visibility = 'private';

        $req = $this->searchRequestCreate($request);
        
        $req->visibility($visibility);
        
        $results = $this->search($req, function ($_request) use ($user) {
            if ($_request->isPageRequested() && ! $_request->isSearchRequested()) {
                $all_query = DocumentDescriptor::local();
                
                $_request->setForceFacetsRequest();
            
                if ($_request->visibility === KlinkVisibilityType::KLINK_PRIVATE) {
                    $all_query = $all_query->private();
                }
                
                return $all_query->orderBy('title', 'ASC');
            }
            
            return false; // force to execute a search on the core instead on the database
        });

        return view('administration.storage.all-files', [
            'pagetitle' => trans('administration.storage.all_files'),
            'documents' => $results ? $results->getCollection() : collect(),
            'context' => 'private',
            'pagination' => $results,
            'is_search_failed' => $results === null,
            'search_terms' => $req->term,
            'facets' => $results !== null ? $results->facets() : [],
            'filters' => $results !== null ? $results->filters() : [],
            'current_visibility' => 'private',
            'is_personal' => false,
            'hint' => false,
            'filter' => trans('administration.storage.all_files')
            ]);
    }
}
