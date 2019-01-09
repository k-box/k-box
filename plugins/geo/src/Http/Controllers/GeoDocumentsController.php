<?php

namespace KBox\Geo\Http\Controllers;

use KBox\User;
use KBox\Shared;
use KBox\Project;
use KBox\Traits\Searchable;
use KBox\DocumentDescriptor;
use Illuminate\Http\Request;
use KBox\Documents\DocumentType;
use Klink\DmsAdapter\Geometries;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard;
use KBox\Http\Controllers\Controller;
use Klink\DmsAdapter\Contracts\KlinkAdapter;
use KBox\Documents\Services\DocumentsService;
use KSearchClient\Model\Search\BoundingBoxFilter;

class GeoDocumentsController extends Controller
{
    use Searchable;
    
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
    public function __construct(KlinkAdapter $adapterService, DocumentsService $documentsService)
    {
        $this->middleware('auth');

        $this->middleware('capabilities');

        $this->middleware('flags:plugins');

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
        $user = $auth->user();

        $req = $this->searchRequestCreate($request);
        
        $req->visibility('private');

        $req->spatialFilter(BoundingBoxFilter::worldBounds()); // add with low priority the World filter to make sure to present only data with geolocation
        
        $documents = $this->getGeodataDocuments($user);

        $results = $this->search($req, function ($_request) use ($documents) {
            
            $_request->setForceFacetsRequest();

            $_request->in($documents->get()->pluck('uuid')->all());

            return false; // force to execute a search on the core instead on the database
        });


        if ($request->wantsJson()) {
            return response()->json($results);
        }

        $filters = $results->filters();

        return view('geo::documents.geo', [
            'pagetitle' => trans('geo::section.page_title'),
            'filter' => trans('geo::section.page_title'),
            'context' => 'documents',
            'documents' => $results,
            'pagination' => $results,
            'search_terms' => $req->term,
            'facets' => $results->facets(),
            'filters' => $filters,
            'spatial_filters' => Geometries::arrayAsLatLngBounds($filters['geo_location'] ?? null) ?? 'null',
            'other_filters' => $this->processOtherFilters(collect($filters)->except('geo_location')),
        ]);
    }

    private function processOtherFilters($filters)
    {
        return $filters->mapWithKeys(function ($value, $key) {
            
            return [$key => is_array($value) ? implode(',', $value) : $value];
        });
    }

    private function getGeodataDocuments(User $user)
    {
        $user_is_dms_manager = $user->isDMSManager();

        $document_ids = collect();

        // private documents directly updated
        $personal_documents = DocumentDescriptor::local()
            ->private()
            ->when(! $user_is_dms_manager, function ($query) use ($user) {
                return $query->ofUser($user->id);
            })
            ->distinct()->select('id');
        
        $document_ids = $document_ids->merge($personal_documents->get());

        // last shared from other users
        $shared_documents = Shared::sharedWithMe($user)
            ->where('shareable_type', '=', DocumentDescriptor::class)
            ->select('shareable_id as id');

        $document_ids = $document_ids->merge($shared_documents->get());

        if (! $user_is_dms_manager) {
            // documents updated in a project I have access to
            $documents_in_projects = $user->projects()->orWhere('projects.user_id', $user->id)->get()->reduce(function ($carry, $prj) {
                return $carry->merge($prj->documents()
                    ->distinct()
                    ->select('id'));
            }, collect());

            $document_ids = $document_ids->merge($documents_in_projects);
        }

        // get the unique ids of the documents, so we don't select the same document more than once
        $list_of_docs = $document_ids->unique(function ($u) {
            return $u['id'];
        });

        // we use "where in" with an array because, in MySQL, union queries cannot be be used as source of the "where in select"
        return DocumentDescriptor::where('document_type', DocumentType::GEODATA)->whereIn('id', $list_of_docs->toArray());
    }
}
