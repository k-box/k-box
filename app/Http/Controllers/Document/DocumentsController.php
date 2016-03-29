<?php namespace KlinkDMS\Http\Controllers\Document;

use Illuminate\Http\Request;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Shared;
use KlinkDMS\Group;
use KlinkDMS\Capability;
use KlinkDMS\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use KlinkDMS\Http\Requests\DocumentAddRequest;
use KlinkDMS\Http\Requests\DocumentUpdateRequest;
use KlinkDMS\Exceptions\FileAlreadyExistsException;
use KlinkDMS\Exceptions\FileNamingException;
use KlinkDMS\Exceptions\ForbiddenException;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use KlinkDMS\Pagination\LengthAwarePaginator as Paginator;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use KlinkDMS\Traits\Searchable;

class DocumentsController extends Controller {
	
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
	
	// private $searchService = null;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(\Klink\DmsDocuments\DocumentsService $adapterService/*, \Klink\DmsSearch\SearchService $searchService*/)
	{
            
		$this->middleware('auth', ['except' => ['showByKlinkId']]);

		$this->middleware('capabilities', ['except' => ['showByKlinkId']]);

		$this->service = $adapterService;
		
		// $this->searchService = $searchService;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(AuthGuard $auth, Request $request, $visibility = 'private' )
	{
		
		$user = $auth->user();

		if(!$user->isDMSManager() && $visibility==='private'){
			$visibility = 'personal';
		}
		
		$filtered_ids = false;
		$pagination = false;
		$showing_only_local_public = false;
		
		$is_personal = $visibility === 'personal' ? true : false;
		if($is_personal) {
			$visibility = 'private';
		}

		$req = $this->searchRequestCreate($request);
		
		$req->visibility($visibility);
        
		$results = $this->search($req, function($_request) use($is_personal, $user) {
			
			if($_request->visibility === \KlinkVisibilityType::KLINK_PUBLIC){
				// if public => return direct search because we want them to see the public network
				return false;
			}
			
			if($is_personal) {
				
				$personal_doc_id = DocumentDescriptor::local()->private()->ofUser($user->id)->get(array('local_document_id'))->fetch('local_document_id')->all();
                
				$_request->in($personal_doc_id);

                $accessible = $this->service->getCollectionsAccessibleByUser($user);
                
                if(!is_null($accessible->personal)){
                    $group_ids = $accessible->personal->map(function($grp){
                        return $grp->toKlinkGroup();	
                    })->all();
                    
                    $_request->on($group_ids);
                }

			}
			
			if($_request->isPageRequested() && !$_request->isSearchRequested()){
				$all_query = DocumentDescriptor::local();
				
				$_request->setForceFacetsRequest();
			
				if($_request->visibility === \KlinkVisibilityType::KLINK_PRIVATE){
					$all_query = $all_query->private();
					if($is_personal){
						$all_query = $all_query->ofUser($user->id);
					}
				}
				
				
				
				return $all_query->orderBy('title', 'ASC');
			}
			
			
			
			
			return false; // force to execute a search on the core instead on the database
		}, function($res_item){
            $local = DocumentDescriptor::where('local_document_id', $res_item->getLocalDocumentID())->first();
			return !is_null( $local ) ? $local : $res_item;
		});

		// Adding user's root groups and institution level groups to the result
		// $groups = Group::roots()->private($auth->user()->id)->orPublic()->get();

		return view('documents.documents', [
			'pagetitle' => (is_null($visibility) ? '': trans('documents.menu.' . ($is_personal ? 'personal' : $visibility)) .' ') . trans('documents.page_title'), 
			'documents' => $results->getCollection(), /*'collections' => $groups,*/ 
			'context' => is_null($visibility) ? 'all' : $visibility,
			'pagination' => $results,
			'search_terms' => $req->term,
			'facets' => $results->facets(),
			'filters' => $results->filters(),
			'current_visibility' => $is_personal ? 'private' : $visibility,
			'is_personal' => $is_personal,
			'hint' => $showing_only_local_public ? trans('documents.messages.local_public_only') : false,
			'filter' => $is_personal ? 'personal' : $visibility]);
	}
	
	public function recent(AuthGuard $auth, \Request $request)
	{
		
		$base_now = Carbon::now();
		
		$user = $auth->user();
		
		$user_is_dms_manager = $user->isDMSManager(); 
		
		$date_limit = $base_now->copy()->subMonths(3);
		
		$limit = \Config::get('dms.items_per_page');
		$page = $request::input('page', 1);
		
		
		// Last Private Documents
		
		$doc_activities = DocumentDescriptor::local()->private();
		
		if(!$user_is_dms_manager){
			$doc_activities = $doc_activities->ofUser($user->id);
		}
		
		$documents_query = $doc_activities->where('updated_at', '>=', $date_limit)->get(['id'])->fetch('id')->toArray();
		
		// Last Starred
		
		$starred_query = $user->starred()->where('updated_at', '>=', $date_limit)->get(['document_id'])->fetch('document_id')->toArray(); //->with('document')
		
		
		// Last Shared docs
		
		$shared_query = Shared::by($user)->where('updated_at', '>=', $date_limit)->where('shareable_type', '=', 'KlinkDMS\DocumentDescriptor')->get(['shareable_id'])->fetch('shareable_id')->toArray(); //->with('shareable')
		
		// let's make them together'
		
		$all_ids = array_unique(array_merge($documents_query, $starred_query, $shared_query));
		
		
		// get the id of the last (bla bla bla) and group them
		
		$all_query = DocumentDescriptor::whereIn('id', $all_ids);

		
		$all_query = $all_query->orderBy('updated_at', 'DESC');
		
		$total = $all_query->count(); /*\Cache::remember('dms_recent_documents_count', 5, function() use($all_query) {
			return  $all_query->count();
		});*/
		
		$documents = $all_query->forPage($page, $limit)->get(); /*\Cache::remember('dms_recent_documents_page'.$page, 5, function() use($all_query, $page, $limit) {
			return $all_query->forPage($page, $limit)->get();
		});*/
		
		$pagination = new Paginator($documents, 
			$total, 
			$limit, $page, [
            	'path'  => $request::url(),
            	'query' => $request::query(),
        	]);
		
		
		$today = Carbon::today();
		
		
		$init_of_month = $base_now->startOfMonth();
		
		$init_of_month_diff = $base_now->startOfMonth()->diffInDays($today);
		
		$start_of_week = $today->previous(Carbon::MONDAY);
		
		
		$grouped = $documents->groupBy(function($date) use($start_of_week, $init_of_month, $init_of_month_diff) {
			
			if($date->updated_at->isToday()){
				$group = trans('units.today');
			}
			else if($date->updated_at->isYesterday()){
				$group = trans('units.yesterday');
			}
			else if($date->updated_at->diffInDays($start_of_week) <= 6){
				
				$group = trans('units.this_week');
			}
			else if($date->updated_at->diffInDays($init_of_month) < $init_of_month_diff-1){
				$group = trans('units.this_month');
			}
			else {
				$group = trans('units.older');
			}
			
			return $group;
	    });
		
//		dd($grouped);
		
//		dd($documents->toArray());

		return view('documents.recent', [
			'search_terms' => '',
			'pagination' => $pagination,
			'info_message' => $user_is_dms_manager ? trans('documents.messages.recent_hint_dms_manager') : null,
			'list_style_current' => $user->optionListStyle(),
			'pagetitle' => trans('documents.menu.recent') .' ' . trans('documents.page_title'), 
			'documents' => $grouped, 'groupings' => array_keys($grouped->toArray()), /*'collections' => $groups,*/ 'context' => 'recent', 
			'filter' => 'recent']);
	}

	public function trash(AuthGuard $auth)
	{
		
		$user = $auth->user();

		$all = $this->service->getUserTrash($user)->all();		

		return view('documents.trash', [
			'search_terms' => '',
			'pagetitle' => trans('documents.menu.trash'), 
			'documents' => $all,
			'context' => 'trash', 
			'filter' => trans('documents.menu.trash'), 
			'empty_message' => 'Nothing is in trash'
		]);
	}

	public function notIndexed(AuthGuard $auth)
	{

		$all_query = DocumentDescriptor::local();

//		if(!$auth->user()->isContentManager()){
//			$all_query = $all_query->ofUser($auth->user()->id);
//		}

		$all = $all_query->notIndexed()->get();

		return view('documents.documents', ['pagetitle' => trans('documents.menu.not_indexed'), 'documents' => $all, 'context' => 'notindexed', 'filter' => trans('documents.menu.not_indexed'), 'empty_message' => 'All the documents has been correctly added to K-Link.']);
	}

	public function sharedWithMe(AuthGuard $auth, Request $request)
	{
		
		// $with_me = null; /*$by_me = null;*/
		
		$auth_user = $auth->user();
		
		$can_share_with_personal = $auth_user->can_capability(Capability::SHARE_WITH_PERSONAL);

        $can_share_with_private = $auth_user->can_capability(Capability::SHARE_WITH_PRIVATE);
            
        $can_see_share = $auth_user->can_capability(Capability::RECEIVE_AND_SEE_SHARE);
		
		
		$req = $this->searchRequestCreate($request);
		
		$req->visibility('private');
		
		$with_me = $this->search($req, function($_request) use($auth_user, $can_share_with_personal, $can_share_with_private, $can_see_share) {
			
			if(!$can_see_share){
				return new Collection();
			}
			
			
			
			$group_ids = $auth_user->involvedingroups()->get(array('peoplegroup_id'))->fetch('peoplegroup_id')->toArray();
					
			$all_in_groups = Shared::sharedWithGroups($group_ids)->get();
				
			$all_single = Shared::sharedWithMe($auth_user)->with(array('shareable', 'sharedwith'))->get();
			
			$all_shared = $all_single->merge($all_in_groups)->unique();
			
			$shared_docs = $all_shared->fetch('shareable.local_document_id')->all();
			
			$_request->in($shared_docs);
			
			if($_request->isPageRequested()){
				
				$_request->setForceFacetsRequest();
				
				return $all_shared;
				
			}
			
			
			
			return false; // force to execute a search on the core instead on the database
		}, function($res_item){
			// from KlinkSearchResultItem to Shared instance
			return DocumentDescriptor::where('local_document_id', $res_item->localDocumentID)->first();
		});

		return view('documents.sharedwithme', [
			'pagetitle' => trans('documents.menu.shared'), 
			'shared_with_me' => $with_me, 
			'current_visibility' => 'private',
			// 'shared_by_me' => $by_me, 
			'can_share' => $can_share_with_personal || $can_share_with_private,
			'context' => 'shared',
			'filter' => trans('documents.menu.shared'), 
			'pagination' => $with_me,
			'search_terms' => $req->term,
			'facets' => $with_me->facets(),
			'filters' => $with_me->filters(),
			'empty_message' => trans('share.empty_message')]);
		
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(AuthGuard $auth)
	{
		
		$user = $auth->user();

		$visibility = 'private';
		if(!$user->isDMSManager()){
			$visibility = 'personal';
		}
		
		return view('documents.create', [
				'pagetitle' => trans('documents.create.page_title'),  
				'context' => $visibility,
				'filter' => $visibility]);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(AuthGuard $auth, DocumentAddRequest $request)
	{
		// ok here will arrive the file data and some info extracted by the JS
		// context info, like the visibility and maybe the group
		// 
		// private visibility as default

		try{
			
			\Log::info('DocumentsController store', ['request' => $request->all(), 'user' => $auth->user()]);

			if ($request->hasFile('document') && $request->file('document')->isValid())
			{
				$grp = $request->has('group') ? Group::findOrFail($request->input('group')) : null;
				$parent = $grp;

				if($request->has('folder_path')){
					 $folder_path = $request->input('folder_path');
					 $parent = $this->service->createGroupsFromFolderPath($auth->user(), $folder_path, true, true, $grp);
				}

				//test and report exceptions
			    $descr = $this->service->importFile($request->file('document'), $auth->user(), 'private', $parent);
			    
			    if ($request->wantsJson())
				{
					if(!is_array($descr)){
						$descr = array('descriptor' => $descr);
					}
					
					return response()->json($descr);

				}
				else {
					return $this->show($descr->id, $auth);
				}

			    
			}
			else if($request->hasFile('document') && !$request->file('document')->isValid()){

				if ($request->wantsJson()) {
					return new JsonResponse(array('error' => trans('errors.upload.simple', ['description' => $request->file('document')->getErrorMessage()])), 400);
				}
				
				return redirect()->back()->withErrors([
					'error' => trans('errors.upload.simple', ['description' => $request->file('document')->getErrorMessage()])
				]);

			}

			if ($request->wantsJson()) {
				return new JsonResponse(array('error' => trans('errors.unknown')), 400);
			}

			return redirect()->back()->withErrors([
				'error' => trans('errors.unknown')
			]);

		}catch(FileAlreadyExistsException $ex){
			
			\Log::warning('DocumentsController store - File already exists check', ['error' => $ex]);

			if ($request->wantsJson()) {
				return new JsonResponse(array('error' => $ex->getMessage()), 409);
			}
			
			return redirect()->back()->withErrors([
				'error' => $ex->getMessage()
			]);
			

		}catch(FileNamingException $ex){
			
			\Log::warning('DocumentsController store - File Naming Policy check', ['error' => $ex]);

			if ($request->wantsJson()) {
				return new JsonResponse(array('error' => $ex->getMessage()), 409);
			}
			
			return redirect()->back()->withErrors([
				'error' => $ex->getMessage()
			]);

		}catch(\Exception $ex){
			
			\Log::warning('DocumentsController store - ' . $ex->getMessage(), ['error' => $ex]);

			if ($request->wantsJson()) {
				return new JsonResponse(array('error' => $ex->getMessage()), 500);
			}

			return redirect()->back()->withErrors([
				'error' => $ex->getMessage() 
			]);

		}

		// available methods on file http://api.symfony.com/2.5/Symfony/Component/HttpFoundation/File/UploadedFile.html
	}


	private function _showPanel(DocumentDescriptor $document){

		$view_params = array('item' => $document);

		return view('panels.document', $view_params);

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id, AuthGuard $auth)
	{
		try{

			if (\Request::ajax())
			{
				$document = DocumentDescriptor::withTrashed()->findOrFail($id);

				return $this->_showPanel($document);
			}

			return $this->edit($id, $auth);

		}catch(ModelNotFoundException $kex){
			\Log::warning('Document Descriptor not found', ['error' => $kex, 'id' => $id]);
			return view('panels.error', ['error_title' => trans('errors.404_title'), 'message' => $kex->getMessage()]);
		}catch(ForbiddenException $kex){
			\Log::warning('Document Descriptor not accessible by user', ['error' => $kex, 'id' => $id, 'user' => $auth->user()->id]);
            
            return view('panels.error', ['error_title' => trans('errors.403_title'), 'message' => trans('errors.forbidden_see_document_exception')]);
			
		}catch(\Exception $kex){
			\Log::error('Document Descriptor panel show error', ['error' => $kex, 'id' => $id]);
			return view('panels.error', ['message' => $kex->getMessage()]);
		}
	}



	public function showByKlinkId($institution, $local_id)
	{
		try{

			$document = $this->service->getDocument($institution, $local_id);

			return $this->_showPanel($document);

		}catch(\KlinkException $kex){
			\Log::error('Document Descriptor showByKlinkId error', ['error' => $kex, 'institution' => $institution, 'local_id' => $local_id]);
			return view('panels.error', ['message' => $kex->getMessage()]);
		}catch(\Exception $kex){
			\Log::error('Document Descriptor showByKlinkId error', ['error' => $kex, 'institution' => $institution, 'local_id' => $local_id]);
			return view('panels.error', ['message' => $kex->getMessage()]);
		}
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id, AuthGuard $auth)
	{
		try{
            /*
             enable edit view only if the user
             - the owner of the document
             - has access to one the collection containing the document
            */
			$document = DocumentDescriptor::withTrashed()->findOrFail($id);
			$user = $auth->user();
            
            $is_owner = $document->owner_id === $user->id;
            
            // collections in which the document is and that can be seen by the user
            $collections = $this->service->getDocumentCollections($document, $user)->count(); 
            
            // if( !$is_owner && $collections === 0 && !$document->isShared() ){
            //     
            //     throw new ForbiddenException( trans('errors.forbidden_edit_document_exception') , 403);
            // }
            

				$view_params = array(
					'document' => $document,
					'file' => $document->file,
					'can_make_public' => !$document->trashed() && $user->can_capability(Capability::CHANGE_DOCUMENT_VISIBILITY),
					'can_edit_groups' => !$document->trashed() && $user->can_capability(array(Capability::MANAGE_OWN_GROUPS, Capability::MANAGE_PROJECT_COLLECTIONS)),
					'can_upload_file' => !$document->trashed() && $user->can_capability(Capability::UPLOAD_DOCUMENTS),
					'can_edit_document' => !$document->trashed() && $user->can_capability(array(Capability::EDIT_DOCUMENT, Capability::DELETE_DOCUMENT)),
					'versions' => !is_null($document->file) ? $document->file->revisionOfRecursive()->get() : new Collection,
					'pagetitle' => trans('documents.edit.page_title', ['document' => $document->title]),
					'context' => 'document', 'context_document' => $document->id, 'filter' => $document->name,
				);

				return view('documents.edit', $view_params);
		
        }catch(ForbiddenException $kex){
            
            \Log::warning('User tried to edit a document who don\'t has access to', ['error' => $kex, 'user' => $auth->user()->id, 'document' => $id]);
            
			throw $kex;
            
        }catch(\Exception $kex){

            \Log::error('Error generating data for documents.edit view', ['error' => $kex]);
            
			return view('panels.error', ['message' => $kex->getMessage()]);
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id, AuthGuard $auth, DocumentUpdateRequest $request)
	{

		// single descriptor
		// can remove group
		// can add group
		// can change title
		// can change visibility
		// can ...
		// title: string
	    // mime_type: string
	    // visibility: string
	    
	    // user_owner: string
	    // user_uploader: string
	    // abstract: string
	    // language: string
	    // authors: string (serialized)
	    // file_id: File
	    // owner_id: User
	    // created_at
	    // updated_at

		\Log::info('Updating Document', ['params' => $id, 'request' => $request->all()]);

		try{

			$user = $auth->user();

			if(!$user->can_capability(Capability::EDIT_DOCUMENT)){
				throw new ForbiddenException(trans('documents.messages.forbidden'), 1);
			}

			$serv = $this->service;

			$ret = \DB::transaction(function() use($id, $serv, $request, $user){

				$document = DocumentDescriptor::findOrFail($id);

				$group_dirty = false;

				// 'authors' => 'sometimes|required|string|regex:/^[\w\d\s\.\-_\(\)]*/',
				// 'visibility' => 'sometimes|required|string|in:public,private',
			    
			 //    // if this is present a new file version will be created and will inherit the 
				// 'document' => 'sometimes|required|between:0,30000', //new document version
				
				// 'remove_group' => 'sometimes|required|exists:groups,id',
				// 'add_group' => 'sometimes|required|exists:groups,id',
				
				if($request->has('remove_group')){
					 
					 $remove_from_group = $request->input('remove_group');
					 
					 if(!is_array($remove_from_group)){
						 $remove_from_group = array($remove_from_group);
					 }
					 
					 foreach($remove_from_group as $remove_from){
						 $grp = Group::findOrFail($remove_from);
						 
						 $serv->removeDocumentFromGroup($user, $document, $grp, false);
					 }
					 
					 $group_dirty = true;
				}
				
				if($request->has('add_group')){
					
					$add_to_group = $request->input('add_group');
					
					if(!is_array($add_to_group)){
						$add_to_group = array($add_to_group);
					}
					
					foreach($add_to_group as $add_to){
						$grp = Group::findOrFail($add_to);
					
						$serv->addDocumentToGroup($user, $document, $grp, false);
					}
					
					$group_dirty = true;
					
				}


				if($request->has('title') && $request->input('title') !== $document->title){
					$document->title = e($request->input('title'));
				}

				if( ($request->has('abstract') || $request->input('abstract', false) === '') && $request->input('abstract') !== $document->abstract){
					$document->abstract = e($request->input('abstract'));
				}

				if($request->has('language') && $request->input('language') !== $document->language){
					$document->language = e($request->input('language'));
				}

                if($user->can_capability(Capability::CHANGE_DOCUMENT_VISIBILITY)){

                    $add_to_public = false;
                    $remove_from_public = false;
                    if($request->has('visibility') && $request->input('visibility') === \KlinkVisibilityType::KLINK_PUBLIC && !$document->is_public){
                        // if was not public and is marked as public
                        $document->is_public = true;
                        $add_to_public = true;

                        \Log::info('Document add public', ['descriptor' => $document, 'add_to_public' => $add_to_public]);
                    }
                    else if(!$request->has('visibility') && $document->is_public){
                        //was public and is no more marker as public
                        $document->is_public = false;
                        $remove_from_public = true;

                        \Log::info('Document remove from public', ['descriptor' => $document, 'remove_from_public' => $remove_from_public]);
                    }
                
                }

				if($request->has('authors') && $request->input('authors') !== $document->authors){
					$document->authors = e($request->input('authors')); //deve essere un array così poi laravel lo serializza
				}


				// handle new file version
			
				try{

					if ($request->hasFile('document') && $request->file('document')->isValid())
					{
						\Log::info('Update Document with new version');
		

						//test and report exceptions
					    $file_model = $this->service->createFileFromUpload($request->file('document'), $user, $document->file);

					    $document->file_id = $file_model->id;
					    $document->mime_type = $file_model->mime_type;

					    
					}
					else if($request->hasFile('document') ){

						throw new Exception(trans('errors.upload.simple', ['description' => $request->file('document')->getErrorMessage()]), 400);

					}

				}catch(FileAlreadyExistsException $fex){

					throw new Exception(trans('documents.versions.filealreadyexists'), 10, $fex);
					
				}

				// save everything if the descriptor isDirty and do the reindex if necesary
				
				if($document->isDirty() || $group_dirty){
					
					$document->save();

					$this->service->reindexDocument($document, \KlinkVisibilityType::KLINK_PRIVATE);

					if( (isset($add_to_public) && $add_to_public) || $document->is_public){
						$this->service->reindexDocument($document, \KlinkVisibilityType::KLINK_PUBLIC);
					}
					else if($remove_from_public){
						$this->service->deletePublicDocument($document);
					}
				}
				else {
					$document->touch();
				}

				

				return $document;
			});

			

			if ($request->ajax() && $request->wantsJson())
			{
				return new JsonResponse($ret, 200);
			}

			return redirect()->route('documents.edit', $id)->with([
	            'flash_message' => trans('documents.messages.updated')
	        ]);

		}catch(\Exception $kex){

			\Log::error('Document updating error', ['error' => $kex, 'id' => $id]);

			$status = array('status' => 'error', 'message' =>  trans('documents.update.error', ['error' => $kex->getMessage()]));

			if ($request->ajax() && $request->wantsJson())
			{
				return new JsonResponse($status, 500);
			}

			return redirect()->route('documents.edit', $id)->withInput()->withErrors([
	            'error' => trans('documents.update.error', ['error' => $kex->getMessage()])
	        ]);
			
		}

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(AuthGuard $auth, \Request $request, $id)
	{
		
		try{
			
			$user = $auth->user();
			
			if(!$user->can_capability(Capability::DELETE_DOCUMENT)){
				throw new ForbiddenException(trans('documents.messages.delete_forbidden'), 1);
			}
			
			
			$descriptor = DocumentDescriptor::findOrFail($id);
			
			if($descriptor->isPublic() && !$user->can_capability(Capability::CHANGE_DOCUMENT_VISIBILITY)){
				\Log::warning('User tried to delete a public document without permission', ['user' => $user->id, 'document' => $id]);
				throw new ForbiddenException(trans('documents.messages.delete_public_forbidden'), 2);
			}
			
			$force = $request::input('force', false);
			
			if($force && !$user->can_capability(Capability::CLEAN_TRASH)){
				\Log::warning('User tried to force delete a document without permission', ['user' => $user->id, 'document' => $id]);
				throw new ForbiddenException(trans('documents.messages.delete_force_forbidden'), 2);
			}
	
			\Log::info('Deleting Document', ['params' => $id]);
	
			if(!$force){
				$this->service->deleteDocument($descriptor);
			}
			else {
				$this->service->permanentlyDeleteDocument($descriptor);
			}

			

			if (\Request::ajax() && \Request::wantsJson())
			{
				return new JsonResponse(array('status' => 'ok'), 202);
			}

			return response('ok', 202);

		}catch(\Exception $kex){

			\Log::error('Document deleting error', ['error' => $kex, 'id' => $id]);

			$status = array('status' => 'error', 'message' =>  trans('documents.bulk.remove_error', ['error' => $kex->getMessage()]));

			if ($request->ajax() && $request->wantsJson())
			{
				return new JsonResponse($status, 422);
			}

			return response('error');
			
		}

	}
	

}
