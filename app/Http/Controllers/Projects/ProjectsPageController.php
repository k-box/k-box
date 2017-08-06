<?php

namespace KlinkDMS\Http\Controllers\Projects;

use Illuminate\Http\Request as IlluminateRequest;
use KlinkDMS\Project;
use KlinkDMS\DocumentDescriptor;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use KlinkDMS\Traits\Searchable;
use KlinkDMS\Http\Controllers\Controller;

/**
 * Handle the Unified Search project page (issue klinkdms/dms#699)
 * and the project information panel
 */
class ProjectsPageController extends Controller
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

        $this->middleware('capabilities');
    }

    /**
     * Shows the projects page as expected by the Unified Search.
     * i.e. the Project menu item on top of the projects collections
     *
     * //TODO: (question for the future): is not the case that project management index and projects page are handled by the same controller?
     * @return \Illuminate\Http\Response
     */
    public function index(Guard $auth, IlluminateRequest $request)
    {
        $user = $auth->user();

        $req = $this->searchRequestCreate($request);
        $req->visibility('private'); // always set visibility to private
        
        $results = $this->search($req, function ($_request) use ($user) {
            $managed = $user->managedProjects()->get(['projects.id']);

            $added_to = $user->projects()->get(['projects.id']);

            $all_projects = $managed->merge($added_to)->pluck('id')->toArray();

            $_request->inProject($all_projects);
            
            if ($_request->isPageRequested() && ! $_request->isSearchRequested()) {
                $all_query = Project::whereIn('id', $all_projects)
                    ->orderBy('name', 'ASC')
                    ->with(['manager', 'users']);

                $_request->setForceFacetsRequest();

                return $all_query;
            }
            
            return false; // force to execute a search on the core instead on the database
        }, function ($res_item) {
            $local = DocumentDescriptor::where('local_document_id', $res_item->getLocalDocumentID())->first();
            return ! is_null($local) ? $local : $res_item;
        });

        $view_parameters = [
            'pagetitle' => trans('projects.page_title'),
            'context' => 'projectspage',
            'pagination' => $results,
            'search_terms' => $req->term,
            'is_search_requested' => $req->isSearchRequested(),
            'facets' => $results->facets(),
            'filters' => $results->filters(),
            'current_visibility' => 'private',
            // 'empty_message' => $req->isSearchRequested() ? trans('documents.messages.no_documents') : trans('documents.messages.no_projects'),
            'filter' => trans('projects.all_projects') // in the search field placeholder
        ];

        if ($req->isSearchRequested()) {
            $view_parameters['documents'] = $results->getCollection();
        } else {
            $view_parameters['projects'] = $results->getCollection();
        }

        return view('documents.projects.projectspage', $view_parameters);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Guard $auth, \Request $request, $id)
    {
        try {
            $user = $auth->user();
            
            $project = Project::findOrFail($id)->load(['users', 'manager', 'microsite']);
            
            // if ($request::wantsJson())
            // {
            //     return response()->json($project);
            // }
    
            return view('documents.projects.detail', [
                'pagetitle' => trans('projects.page_title_with_name', ['name' => $project->name]),
                'project' => $project,
                'project_users' => $project->users()->orderBy('name', 'ASC')->get(),
            ]);
        } catch (\Exception $ex) {
            \Log::error('Error showing project', ['context' => 'ProjectsPageController', 'params' => $id, 'exception' => $ex]);

            // if ($request::wantsJson())
            // {
            //     return new JsonResponse(array('status' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])), 500);
            // }
            
            return redirect()->back()->withErrors(
                ['error' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])]
              );
        }
    }
}
