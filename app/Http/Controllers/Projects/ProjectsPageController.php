<?php

namespace KBox\Http\Controllers\Projects;

use KBox\User;
use KBox\Project;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use KBox\Traits\Searchable;
use KBox\Http\Controllers\Controller;
use KBox\Pagination\SearchResultsPaginator as Paginator;
use Klink\DmsSearch\SearchRequest;

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
    }

    /**
     * Shows the projects page as expected by the Unified Search.
     * i.e. the Project menu item on top of the projects collections
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Guard $auth, Request $request)
    {
        $this->authorize('viewAny', Project::class);

        $user = $auth->user();

        $req = $this->searchRequestCreate($request);
        $req->visibility('private'); // always set visibility to private
        
        $all_projects_ids = $this->getUserAccessibleProjects($user);

        $results = $all_projects_ids->isEmpty() ? $this->getEmptyResult($req) : $this->search($req, function ($_request) use ($all_projects_ids) {
            $all_projects = $all_projects_ids->toArray();

            if ($_request->getFilter('properties.tags')->isEmpty()) {
                $_request->inProject($all_projects);
            }
            
            if ($_request->isPageRequested() && ! $_request->isSearchRequested()) {
                $all_query = Project::whereIn('id', $all_projects)
                    ->orderBy('name', 'ASC')
                    ->with(['manager', 'users']);

                $_request->setForceFacetsRequest();

                return $all_query;
            }
            
            return false; // force to execute a search on the core instead on the database
        });

        $view_parameters = [
            'pagetitle' => trans('projects.page_title'),
            'context' => 'projectspage',
            'pagination' => $results,
            'search_terms' => $req->term,
            'is_search_requested' => $req->isSearchRequested(),
            'facets' => $results && ! $all_projects_ids->isEmpty() ? $results->facets() : [],
            'filters' => $results && ! $all_projects_ids->isEmpty() ? $results->filters() : [],
            'current_visibility' => 'private',
            'filter' => trans('projects.all_projects') // in the search field placeholder
        ];

        if ($req->isSearchRequested()) {
            $view_parameters['documents'] = $results->getCollection();
        } else {
            $view_parameters['projects'] = $results->getCollection();
        }

        return view('documents.projects.projectspage', $view_parameters);
    }

    private function getEmptyResult(SearchRequest $req)
    {
        $pagination = new Paginator(
            $req->term === '*' ? '' : $req->term,
            collect(),
            [],
            [],
            0,
            $req->limit,
            $req->page,
            [
                'path'  => $req->url,
                'query' => $req->query,
            ]
        );
            
        return $pagination;
    }

    private function getUserAccessibleProjects(User $user)
    {
        $managed = $user->managedProjects()->get(['projects.id']);

        $added_to = $user->projects()->get(['projects.id']);

        return $managed->merge($added_to)->pluck('id');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Guard $auth, Request $request, $id)
    {
        $this->authorize('viewAny', Project::class);

        try {
            $project = Project::findOrFail($id)->load(['users', 'manager', 'microsite']);
    
            return view('documents.projects.detail', [
                'pagetitle' => trans('projects.page_title_with_name', ['name' => $project->name]),
                'project' => $project,
                'project_users' => $project->users()->orderBy('name', 'ASC')->get(),
            ]);
        } catch (\Exception $ex) {
            \Log::error('Error showing project', ['context' => 'ProjectsPageController', 'params' => $id, 'exception' => $ex]);
            
            return redirect()->back()->withErrors(
                ['error' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])]
            );
        }
    }
}
