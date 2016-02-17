<?php namespace KlinkDMS\Http\Controllers;

use Illuminate\Http\Request;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Group;
use KlinkDMS\PeopleGroup;
use KlinkDMS\Capability;
use KlinkDMS\Shared;
use KlinkDMS\User;
use Illuminate\Http\JsonResponse;
use KlinkDMS\Http\Requests\CreateShareRequest;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use Illuminate\Database\Eloquent;
use Illuminate\Support\Collection;
use KlinkDMS\Traits\Searchable;

/**
 * Manage you personal shares
 */
class SharingController extends Controller {
	
	use Searchable;


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
	public function __construct(\Klink\DmsDocuments\DocumentsService $adapterService)
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
		
// 		$group_ids = $user->involvedingroups()->get(array('peoplegroup_id'))->fetch('peoplegroup_id')->toArray();
// 		
// 		$all_in_groups = Shared::sharedWithGroups($group_ids)->get();
// 		
// 		$all_single = Shared::sharedWithMe($user)->with(array('shareable', 'sharedwith'))->get();
// 
// 		$all = $all_single->merge($all_in_groups)->unique();
		
		
		$req = $this->searchRequestCreate($request);
		
		$req->visibility('private');
		
		$all = $this->search($req, function($_request) use($user) {
			
			$group_ids = $user->involvedingroups()->get(array('peoplegroup_id'))->fetch('peoplegroup_id')->toArray();
					
			$all_in_groups = Shared::sharedWithGroups($group_ids)->get();
			
				
			$all_single = Shared::sharedWithMe($user)->with(array('shareable', 'sharedwith'))->get();
			
			$all_shared = $all_single->merge($all_in_groups)->unique();
			
			$shared_docs = $all_shared->fetch('shareable.local_document_id')->all();
			$shared_files_in_groups = array_flatten(array_filter($all_shared->map(function($g){
				if($g->shareable_type === 'KlinkDMS\Group'){
					return $g->shareable->documents->fetch('local_document_id')->all();
				} 
				return null;
				})->all()));
			
			// dd(compact('all_single', 'shared_groups'));
			
			$_request->in(array_merge($shared_docs, $shared_files_in_groups));
			// $_request->on();
			
			if($_request->isPageRequested()){
				
				$_request->setForceFacetsRequest();
				
				return $all_shared;
				
			}
			
			return false; // force to execute a search on the core instead on the database
		}, function($res_item){
			// from KlinkSearchResultItem to Shared instance
			return DocumentDescriptor::where('local_document_id', $res_item->localDocumentID)->first();
		});
		

		return view('share.list', [
			'shares' => $all,
			'pagetitle' => trans('share.page_title'),
			'context' => 'sharing',
			'current_visibility' => 'private',
			'pagination' => $all,
			'search_terms' => $req->term,
			'facets' => $all->facets(),
			'filters' => $all->filters(),
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(AuthGuard $auth, Request $request)
	{

		$me = $auth->user();

		$groups_input = $request->input('groups', array());
		$documents_input = $request->input('documents', array());

		$groups_req = is_array($groups_input) ? $groups_input : array_filter(explode(',', $request->input('groups', '')));

		$documents_req = is_array($documents_input) ? $documents_input : array_filter(explode(',', $request->input('documents', '')));


		// TODO: documents can be public, so we need to convert to local cached documents before can be used


		$documents = DocumentDescriptor::whereIn('id', $documents_req)->get();

		$groups = Group::whereIn('id', $groups_req)->get();


		$all_in = $documents->merge($groups);
		
		$first = $all_in->first();

		$elements_count = $all_in->count(); //count($groups_req) + count($documents_req);
		
		
		$available_users = User::whereNotIn('id', array($me->id))->whereHas('capabilities', function($q)
		{
		    $q->where('key', '=', Capability::RECEIVE_AND_SEE_SHARE);
		
		})->get();
		
		
		$can_institutional = $me->can_capability(Capability::SHARE_WITH_PRIVATE);
		
		$can_personal = $me->can_capability(Capability::SHARE_WITH_PERSONAL);
		
		$people_query = PeopleGroup::with('people');
		
		$people = null;
		
		if($can_personal && $can_institutional){
			$people = PeopleGroup::all()->load('people');
		}
		else {
			if($can_institutional){
				$people_query = $people_query->institutional();
			}
			else if($can_personal){
				$people_query = $people_query->personal($me->id);
			}
			$people = $people_query->get(); 
		}
		

		return view('panels.share_create', [
			'users' => $available_users, 
			'people' => $people,
			'documents' => $documents, 'has_documents' => count($documents_req) > 0, 
			'has_groups' => count($groups_req) > 0, 'groups' => $groups,
			'panel_title' => trans_choice('share.share_panel_title_alt', $elements_count, ['count' => $elements_count-1, 'what' => $first instanceof Group ? $first->name : $first->title]),
			'elements_count' => $elements_count ]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(AuthGuard $auth, CreateShareRequest $request)
	{

		// with_users
		// with_people
		// groups
		// documents

		$status = \DB::transaction(function() use($auth, $request){

			$user = $auth->user();

			$users_to_share_with = $request->input('with_users', array());
			$people_to_share_with = $request->input('with_people', array());

			$groups = $request->has('groups') ? $request->input('groups') : array();

			$documents = $request->has('documents') ? $request->input('documents') : array();

			$groups_to_share = Group::whereIn('id', $groups)->get();
			
			$documents_to_share = DocumentDescriptor::whereIn('id', $documents)->get();
			
			$user_dest = User::whereIn('id', $users_to_share_with)->get();
			
			$people_dest = PeopleGroup::whereIn('id', $people_to_share_with)->get();
			
			$shares_list = $this->createShare($groups_to_share->merge($documents_to_share), $user_dest->merge($people_dest), $user);

			
			return ['status' => 'ok', 'message' => trans_choice('share.share_created_msg', $shares_list->count(), ['num' => $shares_list->count()])];
		});

		if ($request->wantsJson()) {
			return new JsonResponse($status, 204);
		}

		return view('panels.share_done', $status);
	}



	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id -> TOKEN
	 * @return Response
	 */
	public function show(AuthGuard $auth, Request $request, $id)
	{
		$share = null;

		if(is_int($id)){

			$share = Shared::findOrFail($id);

		}
		else {

			$share = Shared::token($id)->first();

		}

		if(is_null($share)){

			throw (new ModelNotFoundException)->setModel('KlinkDMS/Shared');
		}

		$share = $share->load('shareable');

		if ($request->wantsJson()) {
			return new JsonResponse($share->toArray(), 200);
		}

		// dd($share);

		return view('share.view', ['share' => $share]);
	}



	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(AuthGuard $auth, $id)
	{
		// Share edit panel??!?!??!
		// 
		return view('share.create', ['edit' => true]);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(AuthGuard $auth, CreateShareRequest $request, $id)
	{
		$share = Shared::findOrFail($id);
	}
	
	
	private function _destroy($id){
		
		$share = Shared::findOrFail($id);

		$share->delete();
		
		//TODO: send email to the user to tell him that the sare is no more available
		//TODO: send a notification to the user that the share is no more available
		//TODO: event for share removed ??!?!?!?!?
		
		return true;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(AuthGuard $auth, Request $request, $id)
	{
		dd('here');
		$executed = $this->_destroy($id);
		
		$status = ['status' => 'ok', 'message' => trans_choice('share.bulk_destroy', 1, ['error' => ''])];
		
		if ($request->wantsJson()) {
			return new JsonResponse($status, 200);
		}

		return response($status);
	}
	
	
	public function deleteMultiple(AuthGuard $auth, Request $request){
		
		$ids = $request->input('shares', array());
		
		if(empty($ids)){
			response(['status' => 'ok']); 
		}
		
		$errors = array();
		
		foreach($ids as $id){
			
			try{
				$executed = $this->_destroy($id);
			}catch(\Exception $ex){
				\Log::error('Sharing Destroy error', ['error' => $kex, 'share_id' => $id, 'request' => $request]);
				$errors[] = $ex->getMessage();
			}
			
		}
		
		$status = ['status' => !empty($errors) ? 'errors' : 'ok', 'message' => trans_choice('share.bulk_destroy', count($errors), ['errors' => implode(',', $errors)])];
		
		if ($request->wantsJson()) {
			return new JsonResponse($status, 200);
		}

		return response($status);
	}
	
	/**
		@param $what Group and Descriptor
		@param $with User and PeopleGroup
	*/
	private function createShare(Collection $what, Collection $with, User $by){
		
//		dd(compact('what', 'with', 'by'));
		
		$token_content = '.';
		
			$shares_list = new Collection;

			foreach ($with as $target) {
				
				foreach($what as $object){
					
					$token_content = $by->id . $target->id . get_class($target) . time() . $object->id . get_class($object);
					
					$shares_list->push($object->shares()->create(array(
						'user_id' => $by->id,
						'sharedwith_id' => $target->id, //the id 
						'sharedwith_type' => get_class($target), //the class
						'token' => hash( 'sha512', $token_content ),
					)));
				}

			}
			
		
		return $shares_list;
			// TODO: now send a notification/mail to every user about their new shares
	}
	
	
	
	public function showGroup(AuthGuard $auth, Request $request, $id){
		
				
		// if shareable == group, Search is possible
		
		$group = Group::findOrFail($id);
		
		// $all = $group->documents()->get();
		
		$req = $this->searchRequestCreate($request);
		
		$req->visibility('private');
		
		$all = $this->search($req, function($_request) use($group) {
			
			$group_ids = array($group->toKlinkGroup()); 
			
			$group_ids = array_merge($group_ids, $group->getDescendants()->map(function($grp){
				return $grp->toKlinkGroup();	
			})->all());
			
			$_request->on($group_ids);
			
			if($_request->isPageRequested() && !$_request->isSearchRequested()){

				$_request->setForceFacetsRequest();

				return $group->documents()->get();
			}
			
			return false;
			
		}, function($res_item){
			return DocumentDescriptor::where('local_document_id', $res_item->localDocumentID)->first();
		});
		
		

		return view('share.list', [
			'shares' => $all,
			'pagetitle' => $group->name . ' - ' . trans('share.page_title'),
			'shared_group' => $group,
			'context' => 'sharing',
			'current_visibility' => 'private',
			'pagination' => $all,
			'search_terms' => $req->term,
			'facets' => $all->facets(),
			'filters' => $all->filters(),
		]);
		
	}

}
