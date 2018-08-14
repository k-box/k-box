<?php

namespace KBox\Http\Controllers\Document;

use KBox\Http\Requests\StarredRequest;
use KBox\Http\Controllers\Controller;
use KBox\Starred;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use KBox\Traits\Searchable;
use Illuminate\Http\Request;

class StarredDocumentsController extends Controller
{
    use Searchable;

    // USER + DESCR ID (INST + LOCAL DOC ID)
    
    /**
     * [$adapter description]
     * @var \Klink\DmsAdapter\KlinkAdapter
     */
    private $service = null;

    private $documentsService = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(\Klink\DmsAdapter\Contracts\KlinkAdapter $adapterService, \KBox\Documents\Services\DocumentsService $documentsService)
    {
        $this->middleware('auth');

        $this->middleware('capabilities');

        $this->service = $adapterService;
        $this->documentsService = $documentsService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Guard $auth, Request $request)
    {
        $req = $this->searchRequestCreate($request);
        
        $req->visibility('private');
        
        $user = $auth->user();
        
        $results = $this->search($req, function ($_request) use ($user) {
            $all_starred = Starred::with('document')->ofUser($user->id);
            
            $personal_doc_id = collect($all_starred->get()->map->document)->map->uuid;

            $_request->in($personal_doc_id->toArray());
            
            if ($_request->isPageRequested() && ! $_request->isSearchRequested()) {
                $_request->setForceFacetsRequest();

                return $all_starred;
            }
            
            return false; // force to execute a search on the core instead on the database
        });
    
        if ($request->wantsJson()) {
            return response()->json($results);
        }

        return view('documents.starred', [
            'pagetitle' => trans('starred.page_title'),
            'filter' => trans('starred.page_title'),
            'context' => 'starred',
            'starred' => $results,
            'pagination' => $results,
            'search_terms' => $req->term,
            'facets' => $results->facets(),
            'filters' => $results->filters(),
            'empty_message' => ($results->count()==0 && $req->term !== '*') ? trans('search.no_results_no_markup', ['term' => $req->term, 'collection' =>  trans('starred.page_title')])  : trans('starred.empty_message')
        ]);
    }

    public function show($id)
    {
        $star = Starred::with('document')->findOrFail($id);

        return view('panels.document', ['item' => $star->document]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Guard $auth, StarredRequest $starredRequest)
    {
        try {
            $doc = $this->documentsService->getDocument($starredRequest->descriptor, $starredRequest->visibility);

            $user_id = $auth->user()->id;

            if (! Starred::existsByDocumentAndUserId($doc->id, $user_id)) {
                $newStar = Starred::firstOrCreate([
                    'user_id' => $user_id,
                    'document_id' => $doc->id
                    ]);

                return new JsonResponse(['status' => 'created', 'id' => $newStar->id], 201);
            } else {
                return response()->json(['status' => trans('starred.already_exists')]);
            }

            return response()->json();
        } catch (\InvalidArgumentException $ex) {
            \Log::error('Error while starring a document', ['context' => 'StarredDocumentsController', 'params' => $starredRequest, 'exception' => $ex]);

            return new JsonResponse(['error' => trans('starred.errors.invalidargumentexception', ['exception' => $ex->getMessage()])], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $star = Starred::findOrFail($id);

        $executed = $star->delete();

        if ($executed) {
            return response()->json(['status' => 'ok']);
        }

        return response()->json(['status' => 'error']);
    }
}
