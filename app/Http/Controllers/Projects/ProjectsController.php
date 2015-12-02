<?php namespace KlinkDMS\Http\Controllers\Projects;

use KlinkDMS\Http\Requests\ProjectRequest;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\PeopleGroup;
use KlinkDMS\User;
use KlinkDMS\Project;
use KlinkDMS\Shared;
use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use KlinkDMS\Pagination\LengthAwarePaginator as Paginator;
use Klink\DmsDocuments\DocumentsService;

class ProjectsController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(/*\Klink\DmsAdapter\KlinkAdapter $adapterService, \Klink\DmsDocuments\DocumentsService $documentsService, \Klink\DmsSearch\SearchService $searchService*/)
	{

		$this->middleware('auth');

		$this->middleware('capabilities');

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Guard $auth, \Request $request)
	{
		$user = $auth->user();
		
		$projects = Project::managedBy($user->id)->get();
		
		if ($request::ajax() && $request::wantsJson())
		{
		    return response()->json($projects);
		}

		return view('projects.index', array(
			'pagetitle' => trans('projects.page_title'), 
			'projects' => $projects
		));
	}


	public function show(Guard $auth, \Request $request, $id)
	{
		try{
			$user = $auth->user();
			
			$project = Project::findOrFail($id)->load(array('users', 'manager'));

			$projects = Project::managedBy($user->id)->get();
			
			if ($request::ajax() && $request::wantsJson())
			{
			    return response()->json($projects);
			}
	
			return view('projects.show', array(
				'pagetitle' => trans('projects.page_title_with_name', ['name' => $project->name]), 
				'projects' => $projects,
				'project' => $project 
			));
		
		}catch(\Exception $ex){

			\Log::error('Error showing project', ['context' => 'ProjectsController', 'params' => $id, 'exception' => $ex]);

			if ($request::ajax() && $request::wantsJson())
			{
			    return new JsonResponse(array('status' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])), 500);
			}
			
			return redirect()->back()->withErrors(
	            ['error' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])]
	          );
			
		}
	}
	
	public function edit(Guard $auth, $id)
	{
		
		$prj = Project::findOrFail($id)->load('users');
		
		$user = $auth->user();

		$available_users = $this->getAvailableUsers($user);

		return view('projects.edit', array(
			'pagetitle' => trans('projects.edit_page_title', ['name' => $prj->name]), 
			'available_users' => $available_users, 
			'manager_id' => $user->id,
			'groups' => PeopleGroup::all(),
			'available_users_encoded' => json_encode($available_users),
			'project' => $prj,
			'project_users' => $prj->users->fetch('id')->all()
		));
	}
	
	
	
	public function create(Guard $auth)
	{
		$user = $auth->user();

		$available_users = $this->getAvailableUsers($user);

		return view('projects.create', array(
			'pagetitle' => trans('projects.create_page_title'), 
			'available_users' => $available_users, 
			'manager_id' => $user->id,
			'groups' => PeopleGroup::all(),
			'available_users_encoded' => json_encode($available_users)
		));
	}
	
	private function getAvailableUsers($current_user){
		return User::whereNotIn('id', array($current_user->id))->get();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Guard $auth, ProjectRequest $request, DocumentsService $service)
	{
		
		try{

			$user = $auth->user();
			
			$manager = $request->has('manager') ? User::findOrFail($request->get('manager')) : $user; 

			$project = \DB::transaction(function() use($manager, $request, $service) {
				
				$name = $request->input('name');
				
				$projectcollection = $service->createGroup($manager, $name, null, null, false);
				
				$newProject = Project::create(array(
					'user_id' => $manager->id,
					'name' => $name,
					'description' => $request->input('description', ''),
					'collection_id' => $projectcollection->id
					));
					
				return $newProject;
			});
			
			\DB::transaction(function() use($project, $request) {
				
				$users = $request->get('users');
				
				foreach($users as $user){
					$project->users()->attach( $user );
				}
				
			});

			
			\Cache::flush();

			if ($request->ajax() && $request->wantsJson())
			{
			    return response()->json($project);
			}
			
			return redirect()->route('projects.show', ['id' => $project->id])->with([
	            'flash_message' => trans('projects.project_created', ['name' => $project->name]) 
	        ]);
			
		
		}catch(\Exception $ex){

			\Log::error('Error creating project', ['context' => 'ProjectsController', 'params' => $request, 'exception' => $ex]);

			if ($request->ajax() && $request->wantsJson())
			{
			    return new JsonResponse(array('status' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])), 500);
			}
			
			return redirect()->back()->withInput()/*->route('projects.create')*/->withErrors(
	            ['error' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])]
	          );
			
		}
	}


	public function update(Guard $auth, ProjectRequest $request, DocumentsService $service, $id) {
		
		try{
			
			$project = Project::findOrFail($id)->load(array('collection', 'users'));

			$user = $auth->user();
			
			$manager = $request->has('manager') ? User::findOrFail($request->get('manager')) : $user; 

			$project = \DB::transaction(function() use($manager, $request, $service, $project) {
			
				if($request->has('name') && $project->name !== $request->input('name')){
					//rename project and collection
					
					$project->name = $request->input('name');
					
					$projectcollection = $service->updateGroup($manager, $project->collection, array('name' => $project->name));
					
					$project->save();
				}
				
				if($request->has('description') && $project->description !== $request->input('description')){
					
					$project->description = $request->input('description');
					$project->save();
				}
				
				
				// test if there are users to add/remove to/from the project
				
					$users = $request->get('users');
					// users are ID
				
					$prj_users = $project->users->fetch('id')->all();
				
					$users_to_add = array_diff($users, $prj_users);
					$users_to_remove = array_diff($prj_users, $users);
				
					if(count($users_to_add) > 0){
						foreach($users_to_add as $user){
							$project->users()->attach( $user );
						}
					}
					
					if(count($users_to_remove) > 0){
						foreach($users_to_remove as $user){
							$project->users()->detach( $user );
						}
					}
				
				return $project->fresh();
			});

			
			\Cache::flush();

			if ($request->ajax() && $request->wantsJson())
			{
			    return response()->json($project);
			}
			
			return redirect()->route('projects.show', ['id' => $project->id])->with([
	            'flash_message' => trans('projects.project_updated', ['name' => $project->name]) 
	        ]);
			
		
		}catch(\Exception $ex){

			\Log::error('Error updating project', ['context' => 'ProjectsController', 'params' => $request, 'exception' => $ex]);

			if ($request->ajax() && $request->wantsJson())
			{
			    return new JsonResponse(array('status' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])), 500);
			}
			
			return redirect()->back()->withInput()/*->route('projects.create')*/->withErrors(
	            ['error' => trans('projects.errors.exception', ['exception' => $ex->getMessage()])]
	          );
			
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

// 		try{
// 			
// 			$executed = \DB::transaction(function() use($id){
// 				
// 				$pgrp = PeopleGroup::findOrFail($id);
// 			
// 				$affectedRows = Shared::sharedWithGroups(array($id))->delete();
// 		
// 				\Log::info('Deleted people group', ['group' => $pgrp, 'shares_deleted' => $affectedRows]);
// 		
// 				return $pgrp->delete();	
// 				
// 			});
// 	
// 			if($executed){
// 				return response()->json( array('status' => 'ok'));
// 			}
// 	
// 			return response()->json( array('status' => 'error'));
// 		
// 		}catch(\Exception $ex){
// 
// 			\Log::error('Error deleting poeple group', ['id' => $id, 'exception' => $ex]);
// 
// 			return new JsonResponse(array('status' => trans('groups.people.invalidargumentexception', ['exception' => $ex->getMessage()])), 500);
// 
// 		}

	}

}
