<?php namespace KlinkDMS\Http\Controllers\Document;

use KlinkDMS\Group;
use KlinkDMS\Http\Requests\Request;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Capability;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use KlinkDMS\Http\Requests\CreateGroupRequest;
use KlinkDMS\Http\Requests\UpdateGroupRequest;
use Illuminate\Support\Collection;
use KlinkDMS\Exceptions\GroupAlreadyExistsException;
use KlinkDMS\Pagination\LengthAwarePaginator as Paginator;

class GroupsController extends Controller {


	/**
	 * [$adapter description]
	 * @var \Klink\DmsDocuments\DocumentsService
	 */
	private $service = null;
	
	private $searchService = null;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(\Klink\DmsDocuments\DocumentsService $service, \Klink\DmsSearch\SearchService $searchService)
	{
            
		$this->middleware('auth');

		$this->middleware('capabilities');

		$this->service = $service;
		
		$this->searchService = $searchService;
	}


	private function echoTree($tree){
		echo '<pre>';

		foreach ($tree as $first_level) {
			echo '|- ' . $first_level->name . '<br/>';

			if($first_level->hasChildren()){
				foreach ($first_level->getChildren() as $second_level) {

					echo '|  |- ' . $second_level->name . '<br/>';

					if($second_level->hasChildren()){
						foreach ($second_level->getChildren() as $third_level) {

							echo '|  |  |- ' . $third_level->name . '<br/>';
							
						}
					}
					
				}
			}
			echo '|<br/>';
		}

		echo '</pre>';
	}


	/**
	 * Display a listing of groups.
	 *
	 * @return Response
	 */
	public function index(AuthGuard $auth, \Request $request)
	{

		// ok famo una prova creiamo dei gruppi e associamoli a un doc e vediamo che succede

		$user = $auth->user();

		if($user->isContentManager()){
			$all = Group::getTree();	
		}
		else {
			$all = Group::roots()->private($user->id)->get();
		}
		
		$private_groups = \Cache::remember('dms_institution_collections', 60, function(){
                    return Group::getTreeWhere('is_private', '=', false);
        });
		
		$personal_groups = \Cache::remember('dms_personal_collections'.$user->id, 60, function() use($user) {
                    return Group::getPersonalTree($user->id);
        });
		
		
		if ($request::ajax() && $request::wantsJson())
		{
		    return new JsonResponse(compact('private_groups', 'personal_groups'), 200);
		}


		return view('groups.manage', [
			'pagetitle' => trans('groups.collections.title'), 
			'context' => 'groups-manage', 
			'filter' => trans('groups.collections.title'), 
			'groups' => $all, 
			'groups_count' => $all->count(),
			'empty_message' => trans('groups.empty_msg')]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		
		$auth_user = \Auth::user();
		
		$view_args = [
			'show_cancel' => true, 'create' => true, 
			'title' => trans('groups.panel_create_title'),
			'main_action' => trans('groups.create_btn')];


		// if context info is available
		
		$is_private = \Request::input('isPrivate', true)  === "false" ? false: true;
		$view_args['private'] = $is_private;
		
		// if($is_private){
		// 	$view_args['collections'] = Group::getPersonalTree($auth_user->id);	
		// }
		// else {
		// 	$view_args['collections'] = Group::getTreeWhere('is_private', '=', false);	
		// }
		
		if(\Request::has('group_context')){
			
			// preselect a parent collection

			$group = Group::findOrFail(\Request::input('group_context', 0));

			$view_args = array_merge($view_args, [
				'show_parent' => true, 
				'parent_label' => $group->name, 
				'parent_id' => $group->id,
				'is_public_collection' => !$group->is_private
			]);
		}
		else {
			
		}

		return view('panels.group', $view_args);
	}

	/**
	 * Store a newly created group in storage.
	 *
	 * @return Response
	 */
	public function store(AuthGuard $auth, CreateGroupRequest $request)
	{
		try{

			$group_name = e($request->input('name'));

			$color = $request->input('color', null);

			$public = $request->input('public', false);

			$parent = $request->input('parent', null);

			$parent_group = is_null($parent) ? $parent : Group::findOrFail($parent);


			$created_group = $this->service->createGroup($auth->user(), $group_name, $color, $parent_group, !$public);


			\Cache::flush();

			if ($request->ajax() && $request->wantsJson())
			{
			    // return response()->json($created_group);
			    return new JsonResponse($created_group->toArray(), 201);
			}
			else if ($request->ajax() && $request->input('ok_template', false))
			{
				return view('groups.tree');
			}

			return response('created', 201);

		}catch(ForbiddenException $fe){
			//return forbidden response

			if ($request->ajax() && $request->wantsJson())
			{
				return new JsonResponse(array('error' => $fe->getMessage()), 403);
			}

			return response("forbidden", 403);
		}catch(GroupAlreadyExistsException $fe){

			if ($request->ajax() && $request->wantsJson())
			{
				return new JsonResponse(array('error' => $fe->getMessage()), 409);
			}

			return response($fe->getMessage(), 409);

		}
		
	}

	/**
	 * Display the specified group.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(AuthGuard $auth, \Request $request, $id)
	{

		$filtered_ids = false;
		$collections = array();
		$all = array();
		$parents = array();
		
		$group = Group::findOrFail($id);
		
		if($this->searchService->hasSearchRequest($request)){

			$id_set2 = array( ($group->is_private ? $group->user_id : '0') . ':' . $group->id );

			$filtered_ids = $this->searchService->searchForId($request, 'private', array(), $id_set2);
			
//			dd(compact('filtered_ids', 'id_set2'));

			$pagination = new Paginator($filtered_ids->ids, 
			$filtered_ids->total_results, 
			26, $filtered_ids->page, [
            	'path'  => $request::url(),
            	'query' => $request::query(),
        	]);
		}

		if(!$filtered_ids){
			
			$all = $group->documents()->get();
	
			$parents = $group->getAncestors()->reverse();
		}
		else {

			$all = DocumentDescriptor::whereIn('hash', $filtered_ids->ids)->get();
		}

		return view('documents.documents', [
			'pagetitle' => $group->name, 
			'documents' => $all, 
			'collections' => $collections, 
			'context' => 'group', 
			'context_group' => $group->id, 
			'context_group_instance' => $group, 
			'filter' => $group->name, 
			'parents' => $parents, 
			'pagination' => ($filtered_ids) ? $pagination : null,
			'search_terms' => ($filtered_ids) ? $filtered_ids->term : '',
			'empty_message' => trans('documents.empty_msg', ['context' => $group->name]) ]);
	}


	public function edit(AuthGuard $auth, $id)
	{

		$selected_group = Group::findOrFail($id);

		$view_args = [
		'show_cancel' => true, 
		'edit' => true, 
		'title' => trans('groups.panel_edit_title', ['name' => $selected_group->name]),
		'main_action' => trans('groups.save_btn'),
		'group' => $selected_group];


		// if context info is available

		// if(\Request::has('group_context')){

		// 	$group = Group::findOrFail(\Request::input('group_context', 0));

		// 	$view_args = array_merge($view_args, ['show_parent' => true, 'parent_label' => $group->name, 'parent_id' => $group->id]);
		// }

		return view('panels.group', $view_args);
	}


	/**
	 * Update the specified group in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(AuthGuard $auth, $id, UpdateGroupRequest $request)
	{
		// update name and color for personal group
		// if operation o

		try{
			
			$group = Group::findOrFail($id);
			
			$user = $auth->user();


			if(!$group->is_private && !$auth->user()->can(Capability::MANAGE_INSTITUTION_GROUPS) ){
				throw new ForbiddenException(trans('errors.group_edit_institution'));
			}

			if(!$auth->user()->can(Capability::MANAGE_OWN_GROUPS) && $group->user_id != $auth->user()->id ){
				throw new ForbiddenException(trans('errors.group_edit_else'));
			}

			$current_parent = $group->getParent();

			if($request->has('name')){

				$group->name = e($request->input('name'));

				$already_exists = $this->service->checkIfGroupExists($user, $group->name, $current_parent, $group->is_private);

				if($already_exists){
					throw new GroupAlreadyExistsException($group->name, $current_parent);
				}

				$group->save();

			}

			if($request->has('color')){

				$group->color = $request->input('color');

				$group->save();

			}

			if(!$request->has('action')){
				if($request->has('public')){
	
					if($group->is_private && !!$request->input('public')){
						// make public
						$this->service->makeGroupPublic($auth->user(), $group);
					}
					// else if(!$group->is_private && !!!$request->input('public')){
					// 	// make private
					// 	$this->service->makeGroupPrivate($auth->user(), $group);
					// }
	
				}
				else if($request->has('private')){
	
					if(!$group->is_private && !!$request->input('private')){
						// make private
						$this->service->makeGroupPrivate($auth->user(), $group);
					}
	
				}
			}

			if($request->has('action') && $request->has('parent')){
				
				//action move (default)

				// $parent = $request->input('parent');

				// // get current parent

				// // TODO: verificare che spostandolo di parent non si abbiano due gruppi con lo stesso nome allo stesso livello
				
				$group = $group->fresh();

				$parent_group = Group::findOrFail($request->input('parent'));

				$already_exists = $this->service->checkIfGroupExists($user, $group->name, $parent_group, $group->is_private);

				if($already_exists){
					throw new GroupAlreadyExistsException($group->name, $parent_group);
				}
				
				if($request->has('public') && $group->is_private && !!$request->input('public')){

					// make public
					$this->service->makeGroupPublic($auth->user(), $group);
	
				}
				else if($request->has('private') && !$group->is_private && !!$request->input('private')){
	
					// make private
					$this->service->makeGroupPrivate($auth->user(), $group);
	
				}

				// $group->moveTo(0, $parent_group);
				$group = $this->service->moveGroup($user, $group, $parent_group);

			}

			\Cache::flush();

			if ($request->ajax() && $request->wantsJson())
			{
			    return response()->json($group);
			}

			return response('updated', 200);

		}catch(ForbiddenException $fe){
			//return forbidden response

			if ($request->ajax() && $request->wantsJson())
			{
				return new JsonResponse(array('error' => $fe->getMessage()), 403);
			}

			return response("forbidden: " . $fe->getMessage(), 403);

		}catch(GroupAlreadyExistsException $fe){

			if ($request->ajax() && $request->wantsJson())
			{
				return new JsonResponse(array('error' => $fe->getMessage()), 409);
			}

			return response($fe->getMessage(), 409);

		}catch(\ExistsException $fe){

			if ($request->ajax() && $request->wantsJson())
			{
				return new JsonResponse(array('error' => $fe->getMessage()), 500);
			}

			return response($fe->getMessage(), 500);

		}
	}

	/**
	 * Remove the specified group from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(AuthGuard $auth, $id)
	{
		// if the group is public only who can manage institution's groups can do this

		try{

			$this->service->deleteGroup($auth->user(), Group::findOrFail($id));
			
			\Cache::flush();

			if ($request::ajax() && $request::wantsJson())
			{
				return new JsonResponse(array('status' => 'ok'), 200);
			}

			return response('ok');

		}catch(ForbiddenException $fe){

			if ($request::ajax() && $request::wantsJson())
			{
				return new JsonResponse(array('error' => $fe->getMessage()), 403);
			}
			
			return response("forbidden", 403);
		}catch(\Exception $fe){

			if ($request::ajax() && $request::wantsJson())
			{
				return new JsonResponse(array('error' => $fe->getMessage()), 500);
			}
			
			return response("generic_error", 500);
		}
	}

}
