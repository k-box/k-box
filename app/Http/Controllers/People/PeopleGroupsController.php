<?php namespace KlinkDMS\Http\Controllers\People;

use KlinkDMS\Http\Requests\PeopleGroupUpdateRequest;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\PeopleGroup;
use KlinkDMS\User;
use KlinkDMS\Shared;
use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use KlinkDMS\Pagination\LengthAwarePaginator as Paginator;

class PeopleGroupsController extends Controller {

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
		
		$can_institutional = $user->can(Capability::MANAGE_PEOPLE_GROUPS);
		
		$can_personal = $user->can(Capability::MANAGE_PERSONAL_PEOPLE_GROUPS);

		
		$groups_query = PeopleGroup::with('people');
		
		$groups = null;
		
		if($can_personal && $can_institutional){
			$groups = PeopleGroup::all()->load('people');
		}
		else {
			if($can_institutional){
				$groups_query = $groups_query->institutional();
			}
			else if($can_personal){
				$groups_query = $groups_query->personal($user->id);
			}
			$groups = $groups_query->get(); 
		}
		// dd(compact('groups_query', 'can_institutional', 'can_personal'));

		

		
		
		//include institution people group only if can be managed
		
		// user that can receive share
		
//
//		if ($request::ajax() && $request::wantsJson())
//		{
//		    return response()->json($all_starred_by_me);
//		}

		$available_users = User::whereNotIn('id', array($user->id))->whereHas('capabilities', function($q)
		{
		    $q->where('key', '=', Capability::RECEIVE_AND_SEE_SHARE);
		})->get();

		return view('groups.people', array(
			'pagetitle' => trans('groups.people.page_title'), 
			'available_users' => $available_users, 
			'available_users_encoded' => json_encode($available_users), 
			'groups' => json_encode($groups),
			'user_can_institutional' => $can_institutional,
		));
	}


	public function show(Guard $auth, $id)
	{
		$group = PeopleGroup::findOrFail($id);
		// $all = $group->documents();
		
		$all = Shared::sharedWithGroups(array($id))->get();
		
		return view('groups.people-explore', [
			'shares' => $all,
			'pagetitle' => $group->name,
			'context' => 'people',
			'people' => $group->people()->get()
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Guard $auth, PeopleGroupUpdateRequest $request)
	{
		
		try{

			$user_id = $auth->user()->id;

//			if(!PeopleGroup::existsByDocumentAndUserId($doc->id, $user_id)){

				$newGroup = PeopleGroup::create(array(
					'user_id' => $user_id,
					'name' => $request->input('name'),
					));

				return new JsonResponse(array('status' => 'ok', 'group' => $newGroup), 201);

//			}
//			else {
//				return response()->json(array('status' => trans('starred.already_exists')));
//			}

		}catch(\InvalidArgumentException $ex){

			\Log::error('Error creating poeple group', ['context' => 'PeopleGroupsController', 'params' => $request, 'exception' => $ex]);

			return new JsonResponse(array('status' => trans('starred.errors.invalidargumentexception', ['exception' => $ex->getMessage()])), 422);

		}catch(\Exception $ex){

			\Log::error('Error creating poeple group', ['context' => 'PeopleGroupsController', 'params' => $request, 'exception' => $ex]);

			return new JsonResponse(array('status' => trans('starred.errors.invalidargumentexception', ['exception' => $ex->getMessage()])), 500);

		}
	}


	public function update(Guard $auth, PeopleGroupUpdateRequest $request, $id) {
		
		try{
			$user = $auth->user();
			$user_id = $user->id;
			
			$group = PeopleGroup::findOrFail($id);
			
			if($group->user_id !== $user_id){
				return new JsonResponse(array('status' => 'The group is not yours, you cannot edit someone elses groups.'), 403);
			}
			
			if($request->has('name')){
				$group->name = e($request->input('name'));
				$group->save();
			}
			
			
			
			if($request->has('make_institutional') && !$user->can(Capability::MANAGE_PEOPLE_GROUPS)){
				throw new \Exception('You cannot edit institutional groups');
			}
			
			if($request->has('action')){
				
				$action = $request->input('action');
				$user_selected = User::findOrFail($request->input('user'));
				
				if($action==='add'){
					$res = $group->people()->attach($user_selected->id);
				}
				else if($action==='remove'){
					$res = $group->people()->detach($user_selected->id);
				}
				
				\Log::info('PeopleGroups action processing', ['action' => $action, 'result' => $res]);
				
			}
			
			if($request->has('make_institutional')){
				$group->is_institution_group = true;
				$group->save();
			}
			
			if($request->has('make_personal')){
				$group->is_institution_group = false;
				$group->save();
			}

			return new JsonResponse(array('status' => 'ok'), 200);

		}catch(\InvalidArgumentException $ex){

			\Log::error('Error creating poeple group', ['context' => 'PeopleGroupsController', 'params' => $request, 'exception' => $ex]);

			return new JsonResponse(array('status' => trans('groups.people.invalidargumentexception', ['exception' => $ex->getMessage()])), 422);

		}catch(\Exception $ex){

			\Log::error('Error creating poeple group', ['context' => 'PeopleGroupsController', 'params' => $request, 'exception' => $ex]);

			return new JsonResponse(array('status' => trans('groups.people.invalidargumentexception', ['exception' => $ex->getMessage()])), 500);

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

		try{
			
			$executed = \DB::transaction(function() use($id){
				
				$pgrp = PeopleGroup::findOrFail($id);
			
				$affectedRows = Shared::sharedWithGroups(array($id))->delete();
		
				\Log::info('Deleted people group', ['group' => $pgrp, 'shares_deleted' => $affectedRows]);
		
				return $pgrp->delete();	
				
			});
	
			if($executed){
				return response()->json( array('status' => 'ok'));
			}
	
			return response()->json( array('status' => 'error'));
		
		}catch(\Exception $ex){

			\Log::error('Error deleting poeple group', ['id' => $id, 'exception' => $ex]);

			return new JsonResponse(array('status' => trans('groups.people.invalidargumentexception', ['exception' => $ex->getMessage()])), 500);

		}

	}

}
