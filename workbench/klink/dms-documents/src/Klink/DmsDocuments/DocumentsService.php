<?php namespace Klink\DmsDocuments;

use Illuminate\Support\ServiceProvider;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\File;
use KlinkDMS\User;
use KlinkDMS\Shared;
use KlinkDMS\Group;
use KlinkDMS\GroupType;
use KlinkDMS\Capability;
use KlinkDMS\Import;
use KlinkDMS\Option;
use KlinkDMS\Project;
use KlinkDMS\Institution;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use KlinkDMS\Exceptions\ForbiddenException;
use KlinkDMS\Exceptions\FileAlreadyExistsException;
use KlinkDMS\Exceptions\FileNamingException;
use Illuminate\Support\Collection;
use KlinkDMS\Http\Request;
use Carbon\Carbon;
use KlinkDMS\Exceptions\GroupAlreadyExistsException;
use Klink\DmsDocuments\TrashContentResponse;
use KlinkDMS\Jobs\ImportCommand;
use Symfony\Component\Debug\Exception\FatalErrorException;
use ErrorException;
use KlinkDMS\Jobs\ThumbnailGenerationJob;

class DocumentsService {

	private $supported_extensions = array(
		'jpg','jpeg','gif','png','txt','csv','rtx','html','htm','rtf',
		'pdf','doc','ppt','xls','docx','xlsx','pptx','odt','odp');

	/**
	 * [$adapter description]
	 * @var \Klink\DmsAdapter\KlinkAdapter
	 */
	private $adapter = null;

	/**
	 * Create a new DocumentsService instance.
	 *
	 * @return void
	 */
	public function __construct(\Klink\DmsAdapter\Contracts\KlinkAdapter $adapter)
	{
		$this->adapter = $adapter;
	}

	
	/**
	 * [getDocument description]
	 * @param  string $institutionID [description]
	 * @param  string $documentID    [description]
	 * @param  string $visibility    [description]
	 * @return KlinkDMS\DocumentDescriptor                [description]
	 */
	public function getDocument($institutionID, $documentID, $visibility='public')
	{
		// if the document is already cached return that instance
		// otherwise make remote call and coversion
		
		$inst_cached = Institution::find($institutionID);
		

		$inst = $inst_cached != null ? $inst_cached : $this->adapter->institutions( $institutionID );

		//so now I have a local id if not already existing

		$cached = DocumentDescriptor::findByInstitutionAndDocumentId($inst->id, $documentID);

		if(is_null($cached)){

			try{

				$klink_descriptor = $this->adapter->getDocument($institutionID, $documentID, $visibility);

				$cached = DocumentDescriptor::fromKlinkDocumentDescriptor($klink_descriptor);

			}catch(\KlinkException $kex){

				$arr1 = compact('institutionID', 'documentID', 'visibility');

				\Log::error('Get Document', array('context' => 'DocumentsService', 'params' => $arr1, 'exception' => $kex));

				throw new \InvalidArgumentException('The specified document is invalid', 2, $kex);
				
			}
		}

		return $cached;
		
	}


	public function getRecentDocuments($days = 7, User $user = null)
	{

		if(!is_null($user)){
			return DocumentDescriptor::ofUser($user->id)->where('created_at', '>=', Carbon::now()->subDays($days))->take(5)->orderBy('created_at', 'DESC')->get();
		}
		else {
			// global recent documents
			return DocumentDescriptor::where('created_at', '>=', Carbon::now()->subDays($days))->take(5)->orderBy('created_at', 'DESC')->get();
		}
	}

	// --- Document Indexing facilities ------------------------

	/**
	 * Utility Function: retrieve the file content or path depending on the fact that the file is indexable.
	 *
	 * If the the File is indexable the path will be returned so the file content can be sent automatically to the core. Otherwise do the best to get the text from the document.
	 *
	 * In case of errors or problems the file name will be returned
	 * 
	 * @param KlinkDMS\File $file The file to be indexed
	 * @return string the file path or the textual content 
	 */
	private function getFileContentForIndexing(File $file){
	
		try{
			
			if($file->isIndexable() && $file->size < 209715200){
				return $file->path;
			}
			
			$instance = app()->make('Klink\DmsDocuments\FileContentExtractor');
			
			return $instance->extract($file->mime_type, $file->path, $file->name);

		}catch(\Exception $ex){
			
			\Log::error('Error getting file content', array('context' => 'DocumentsService', 'param' => $file->toArray(), 'exception' => $ex));
			
			return $file->name;	
		}
		
	}

	/**
	 * Proxy to the adapter->addDocument call to catch indexing error and operating a fallback retry as partially supported file.
	 *
	 * @return KlinkDocumentDescriptor the descriptor returned by the K-Link Core
	 */
	private function addDocumentProxy(DocumentDescriptor $descr, File $file){
		
		$klink_descriptor = $descr->toKlinkDocumentDescriptor();

		$original_hash = $klink_descriptor->hash; 

		$content = $this->getFileContentForIndexing($file);

		if(!is_file($content)){
			$klink_descriptor->hash = \KlinkDocumentUtils::generateHash($content);
		}
		
		$document = new \KlinkDocument($klink_descriptor, $content);
		
		try{
			
			$returned_descriptor = $this->adapter->addDocument( $document );

			$returned_descriptor->hash = $original_hash;

			\Log::info('Core indexDocument returned descriptor for ' . $descr->id, array('context' => 'DocumentsService', 'response' => $returned_descriptor));
			
			return $returned_descriptor;

		}catch(\KlinkException $kex){

			// try again, if fails again throw error
			
			\Log::warning('Index document fallback used for document ' . $descr->id, array('cause' => $kex));
			
			try{

				$alternate_content = !is_null($descr->abstract) && !is_null($descr->abstract) ? $descr->abstract : $descr->title;
				
				$klink_descriptor->hash = \KlinkDocumentUtils::generateHash($alternate_content);
				
				$document = new \KlinkDocument($klink_descriptor, $alternate_content);
				
				$returned_descriptor = $this->adapter->addDocument( $document );

				$returned_descriptor->hash = $original_hash;

				\Log::info('Core indexDocument returned descriptor for ' . $descr->id, array('context' => 'DocumentsService', 'response' => $returned_descriptor));
				
				return $returned_descriptor;
				
			}catch(\KlinkException $kex_internal){
				
				throw $kex_internal;
			
			}
			
		}
	}
	
	private function updateDocumentProxy(DocumentDescriptor $descr, File $file, $visibility){
		
		$klink_descriptor = $descr->toKlinkDocumentDescriptor($visibility == \KlinkVisibilityType::KLINK_PUBLIC);

		$content = $this->getFileContentForIndexing($file);

		$original_hash = $klink_descriptor->hash; 


		if(!is_file($content)){
			$klink_descriptor->hash = \KlinkDocumentUtils::generateHash($content);
		}
		
		$document = new \KlinkDocument($klink_descriptor, $content);
		
		try{
			
			$returned_descriptor = $this->adapter->updateDocument( $document );

			$returned_descriptor->hash = $original_hash;

			\Log::info('Core indexDocument returned descriptor for ' . $descr->id, array('context' => 'DocumentsService', 'response' => $returned_descriptor));
			
			return $returned_descriptor;

		}catch(\KlinkException $kex){

			// try again, if fails again throw error
			
			\Log::warning('Index document fallback used for document ' . $descr->id, array('cause' => $kex));
			
			try{

				$alternate_content = !is_null($descr->abstract) && !is_null($descr->abstract) ? $descr->abstract : $descr->title;

				$klink_descriptor->hash = \KlinkDocumentUtils::generateHash($alternate_content);
				
				$document = new \KlinkDocument($klink_descriptor, $alternate_content);

				$returned_descriptor = $this->adapter->updateDocument( $document );

				$returned_descriptor->hash = $original_hash;

				\Log::info('Core indexDocument returned descriptor for ' . $descr->id, array('context' => 'DocumentsService', 'response' => $returned_descriptor));
				
				return $returned_descriptor;
				
			}catch(\KlinkException $kex_internal){
				
				throw $kex_internal;
			
			}
			
		}

	}


	/**
	 * Index a previously uploaded file (giving the \KlinkDMS\File model instance) into K-Link. At the end of the indexing process a DocumentDescriptor will be created, persisted and returned to the caller.
	 *
	 * If the DocumentDescriptor related to the File hash does not exists a new DocumentDescriptor will be created 
	 * and indexed into the K-Link Core.
	 * If the DocumentDescriptor related to the File hash has been deleted with soft delete, the descriptor will be 
	 * updated according to the File information and re-indexed into the K-link Core
	 * 
	 * @param KlinkDMS\File $file The file to be indexed
	 * @param string $visibility The visibility of the document inside K-Link (private or public). Optional, default value 'public'
	 * @return KlinkDMS\DocumentDescriptor the document descriptor that has been stored on the database
	 * @throws KlinkException If the file cannot be indexed
	 * @throws InvalidArgumentException If the file type could not be indexed, 
	 */
	public function indexDocument(File $file, $visibility = 'public', User $owner = null, Group $group = null, $return_also_if_indexing_error = false)
	{
		
		//TODO: if the same document will be indexed in both public and private what will happen
		

		// if already saved as private and $visibility='public' keep only one record and change visibility to both

		if( DocumentDescriptor::existsByHash($file->hash) ){

			throw new FileAlreadyExistsException($file->name, DocumentDescriptor::findByHash($file->hash));
			
		}

		$institution = $this->adapter->institutions( \Config::get('dms.institutionID') );
		
		if(is_null($owner)){
			$owner = $file->user;
		}

		if(!is_null($owner->institution_id)){
			$institution = $owner->institution;
		}

		$document_type = \KlinkDocumentUtils::documentTypeFromMimeType( $file->mime_type );
		$local_document_id = substr($file->hash, 0, 6);

		$document_url_path = $this->constructUrl($local_document_id, 'document');
		$thumbnail_url_path = $this->constructUrl($local_document_id, 'thumbnail');

		$attrs = array(
            'institution_id' => $institution->id,
            'local_document_id' => $local_document_id,
            'title' => $file->name,
            'hash' => $file->hash,
            'document_uri' => $document_url_path,
            'thumbnail_uri' => $thumbnail_url_path,
            'mime_type' => $file->mime_type,
            'visibility' => $visibility,
            'document_type' => $document_type,
            'user_owner' => $owner->name . ' <' . $owner->email . '>',
            'user_uploader' => $owner->name . ' <' . $owner->email . '>',
            'owner_id' => $owner->id,
            'file_id' => $file->id,
            'created_at' => $file->created_at,
            'status' => DocumentDescriptor::STATUS_PENDING
        );

        \Log::info('Core indexDocument - before saving DocumentDescriptor', array('context' => 'DocumentsService', 'attrs' => $attrs));

		$descr = new DocumentDescriptor($attrs);

		try{

			$descr->save();

			if(!is_null($owner)){
				$descr->owner()->associate($owner);
				$descr->save();
			}

			// Add the descriptor to the given group

			if(!is_null($group)){

				$descr->groups()->save($group);

			}

			$returned_descriptor = $this->addDocumentProxy($descr, $file);

			\Log::info('Core indexDocument', array('context' => 'DocumentsService', 'response' => $returned_descriptor));

			$descr = $descr->mergeWithKlinkDocumentDescriptor( $returned_descriptor );

			$descr->status = DocumentDescriptor::STATUS_INDEXED;
            
            $descr->last_error = null;

			$descr->save();

			\Cache::flush();

			return $descr;

		}catch(\InvalidArgumentException $kex){

			$descr->status = DocumentDescriptor::STATUS_ERROR;
            
            $descr->last_error = $kex;

			$descr->save();

			\Log::critical('K-Link InvalidArgument Exception: ' . $kex->getMessage(), array('context' => 'DocumentsService', 'param' => $file->toArray(), 'exception' => $kex));

			\Log::error('Error indexing document into K-Link', array('context' => 'DocumentsService', 'param' => $file->toArray(), 'exception' => $kex));

			if($return_also_if_indexing_error){
				return $descr;
			}
			
			throw $kex;
		}catch(\KlinkException $kex){

			$descr->status = DocumentDescriptor::STATUS_ERROR;
            
            $descr->last_error = $kex;

			$descr->save();

			\Log::critical('K-Link Core Exception: ' . $kex->getMessage(), array('context' => 'DocumentsService', 'param' => $file->toArray(), 'exception' => $kex));

			\Log::error('Error indexing document into K-Link', array('context' => 'DocumentsService', 'param' => $file->toArray(), 'exception' => $kex));

			if($return_also_if_indexing_error){
				return $descr;
			}
			
			throw $kex;
		}catch(\Exception $kex){

			$descr->status = DocumentDescriptor::STATUS_ERROR;
            
            $descr->last_error = $kex;

			$descr->save();

			\Log::error('Error indexing document into K-Link', array('context' => 'DocumentsService', 'param' => $file->toArray(), 'exception' => $kex));

			if($return_also_if_indexing_error){
				return $descr;
			}
			
			throw $kex;
		}
	}

	/**
	 * Reindex e previously indexed document from it's descriptor.
	 *
	 * Note that not only the descriptor will be updated in the K-Link network, but also the file referenced by the descriptor will be controlled
	 * 
	 * @param  DocumentDescriptor $descriptor The descriptor of the document that needs to the updated on the K-Link Core
	 * @param  string             $visibility The descriptor visibility to reindex
	 * @return KlinkDMS\DocumentDescriptor the document descriptor that has been stored on the database
	 * @throws KlinkException If the file cannot be indexed or something happened on the K-Link Core side
	 * @throws InvalidArgumentException If the file type could not be indexed, 
	 */
	public function reindexDocument(DocumentDescriptor $descriptor, $visibility = null, $force = false)
	{
		
		if(!$descriptor->isMine()){
			//no indexing required because it's not my document
			return $descriptor;
		}


		$descriptor->status = DocumentDescriptor::STATUS_PENDING;

		$descriptor->save();

		if($force){

			$document_url_path = $this->constructUrl($descriptor->local_document_id, 'document');
			$thumbnail_url_path = $this->constructUrl($descriptor->local_document_id, 'thumbnail');

			$descriptor->save();
		}

		try{
			
			$returned_descriptor = $this->updateDocumentProxy($descriptor, $descriptor->file, $visibility);

			\Log::info('Core re-indexDocument', array('context' => 'DocumentsService', 'response' => $returned_descriptor));

			$descriptor = $descriptor->mergeWithKlinkDocumentDescriptor( $returned_descriptor );

			$descriptor->status = DocumentDescriptor::STATUS_INDEXED;
            
            $descriptor->last_error = null;

			$descriptor->save();

			\Cache::flush();

			return $descriptor;
            
        }catch(\InvalidArgumentException $kex){

			$descriptor->status = DocumentDescriptor::STATUS_ERROR;
            
            $descriptor->last_error = $kex;

			$descriptor->save();

			\Log::error('Error re-indexing document into K-Link', array('context' => 'DocumentsService', 'param' => $descriptor->toArray(), 'exception' => $kex));
			
			throw $kex;

		}catch(\KlinkException $kex){

			$descriptor->status = DocumentDescriptor::STATUS_ERROR;
            
            $descriptor->last_error = $kex;

			$descriptor->save();

			\Log::error('Error re-indexing document into K-Link', array('context' => 'DocumentsService', 'param' => $descriptor->toArray(), 'exception' => $kex));

			throw $kex;
			
		}catch(\Exception $kex){

			$descriptor->status = DocumentDescriptor::STATUS_ERROR;
            
            $descriptor->last_error = $kex;

			$descriptor->save();

			\Log::error('Error re-indexing document into K-Link', array('context' => 'DocumentsService', 'param' => $descriptor->toArray(), 'exception' => $kex));

			throw $kex;
			
		}


	}

	public function reindexDocuments($docs, $force = false)
	{

		$errors = array();

		foreach ($docs as $doc) {
			try{
				//if is both private and public reindex on every visibility
				$this->reindexDocument($doc, $doc->visibility, $force);

			}catch(\KlinkException $kex){
				$errors[$doc->id] = $kex;
			}
		}

		if(!empty($errors)){
			throw new \Exception("Some documents have not been reindexed", count($errors));
			
		}
	}

	/**
	 * Delete the public version of the document from the public K-Link Network.
	 *
	 * If the documents does not exists will fail silently
	 *
	 * @param DocumentDescriptor $descriptor
	 */
	public function deletePublicDocument(DocumentDescriptor $descriptor)
	{
		try{
			
			$klink_descriptor = $descriptor->toKlinkDocumentDescriptor(true); //shortcut for faking a public descriptor

			$returned_descriptor = $this->adapter->removeDocument( $klink_descriptor );

			\Log::info('Core deletePublicDocument', array('context' => 'DocumentsService', 'response' => $returned_descriptor));

			\Cache::flush();

		}catch(\KlinkException $kex){

			\Log::error('Error deleting public document from K-Link', array('context' => 'DocumentsService', 'param' => $descriptor, 'exception' => $kex));

		}
	}

	/**
	 * Mark the DocumentDescriptor as deleted and remove the document from the K-Link Network.
	 *
	 * If the DocumentDescriptor has both public and private visibility the parameter visibility must
	 * be specified.
	 *
	 * No File will be deleted from the local storage
	 * 
	 * @param  DocumentDescriptor $descriptor the descriptor that correspond to the document that needs to be de-indexed
	 * @param string|null $visibilityToRemove The visibility of the document that will be removed from the K-Link Network, if not specified the visibility of the descriptor will be used, but if the DocumentDescriptor has both public and private visibility and this parameter is not set an InvalidArgumentException will be thrown.
	 * @return boolean true if the operation has been done
	 * @throws InvalidArgumentException 
	 * @throws KlinkException is something unexpected has occurred
	 */
	public function deleteDocument(User $user, DocumentDescriptor $descriptor, $visibilityToRemove = null)
	{

		if(!$user->can_capability(Capability::DELETE_DOCUMENT)){
			throw new ForbiddenException(trans('documents.messages.delete_forbidden'), 1);
		}

		if($descriptor->isPublic() && !$user->can_capability(Capability::CHANGE_DOCUMENT_VISIBILITY)){
			\Log::warning('User tried to delete a public document without permission', ['user' => $user->id, 'document' => $id]);
			throw new ForbiddenException(trans('documents.messages.delete_public_forbidden'), 2);
		}

		if(!$descriptor->isMine()){
			//no action required because is an institution document saved by the DMS
			return true;
		}
		
		$descriptor->status = DocumentDescriptor::STATUS_REMOVING;

		$descriptor->save();

		$file_id = $descriptor->file->id;
		
		try{
			
			$klink_descriptor = $descriptor->toKlinkDocumentDescriptor();

			$returned_descriptor = $this->adapter->removeDocument( $klink_descriptor );
			
			\Log::info('Core deleteDocument (private)', array('context' => 'DocumentsService', 'response' => $returned_descriptor));
			
			if($descriptor->isPublic()){
				
				$klink_descriptor = $descriptor->toKlinkDocumentDescriptor(true);
				
				$returned_descriptor = $this->adapter->removeDocument( $klink_descriptor );
				
				\Log::info('Core deleteDocument (public)', array('context' => 'DocumentsService', 'response' => $returned_descriptor));
			}

			\Cache::flush();

		}catch(\KlinkException $kex){

			if($kex->getCode() != 404){

				$descriptor->status = DocumentDescriptor::STATUS_ERROR;

				$descriptor->save();

				\Cache::flush();

				\Log::error('Error deleting document from K-Link', array('context' => 'DocumentsService', 'param' => $descriptor, 'exception' => $kex));

				throw $kex;
			}
			
		}

		// if we are here everything is ok from the Core side

		$descriptor->status = DocumentDescriptor::STATUS_NOT_INDEXED;

		$descriptor->save();
		
		$is_deleted = $descriptor->delete();
		
		$destroy_count = File::destroy($file_id);
		
		// remove also the import
		
		Import::fromFile($file_id)->delete();

		return $is_deleted && ($destroy_count == 1);
	}
	
	/**
	 * Permanently removes a document (will be removed from starred, groups and all the revisions will be deleted)
	 */
	public function permanentlyDeleteDocument(User $user, DocumentDescriptor $descriptor){
		
		if(!$descriptor->trashed() && !$descriptor->isMine()){
			$trashed = $this->deleteDocument($descriptor);
			if(!$trashed){
				throw new \Exception('The document cannot be moved to trash automatically');
			}
		}

		if(!$user->can_capability(Capability::CLEAN_TRASH)){
			throw new ForbiddenException(trans('documents.messages.delete_forbidden'), 1);
		}
		
		
		$transaction = \DB::transaction(function() use($descriptor){
		
			\Log::info('Permanently deleting document', ['descriptor' => $descriptor]);
			
			$is_shared = $is_deleted = $descriptor->shares()->count() > 0;
			
			if($is_shared){
				$is_deleted = $descriptor->shares()->delete();
				
				if(!$is_deleted){
					\Log::warning('Delete aborted - share check', compact('is_deleted'));
					throw new \Exception('The Document is shared and cannot be removed from the shares. Please un-share the document and then try to delete it again.');
				}
			}
			
			// Get all the file revisions and remove them
			
			$file = File::withTrashed()->findOrFail($descriptor->file_id);
			
			$file_path = $file->path;
			
			$is_deleted = $descriptor->forceDelete();
			
			$is_deleted = $file->forceDelete();
			
			// if evertyhing on the DB is deleted  remove the File from disk (this action is very risky to perform before cleaning the DB, if error occurs we are in a bad situation)
			
			unlink($file_path);
			
			
			return true;
		});
        
        \Cache::flush();
        
        return $transaction;
		
	}
	
	public function permanentlyDeleteGroup(Group $group, User $user){
		
		if(!$group->trashed()){
			$trashed = $this->deleteGroup($user, $group);
			if(!$trashed){
				throw new \Exception('The document cannot be moved to trash automatically');
			}
		}

		if(!$group->is_private && !$user->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS)){
			throw new ForbiddenException(trans('groups.delete.forbidden_delete_project_collection', ['collection' => $group->name]));
		}

		if($group->is_private && $user->id != $group->user_id && !$user->can_capability(Capability::MANAGE_OWN_GROUPS)){
			throw new ForbiddenException(trans('groups.delete.forbidden_delete_collection', ['collection' => $group->name]));
		}
		
		// $shares_query = $group->shares();
		// $is_shared = $shares_query->count() > 0;
		
		return \DB::transaction(function() use($group){
		
			\Log::info('Permanently deleting group', ['group' => $group]);

			// TODO: remove all shares
			
			
			$is_deleted = $group->forceDelete();

			$group->deleteSubtree(false, true);

			\Cache::flush();
		
			return true;
		});
		
	}
	
	/**
	 *	restore a previously deleted document
	 */
	public function restoreDocument(DocumentDescriptor $descriptor)
	{
		
		\Log::info('Core restoreDocument', array('context' => 'DocumentsService', 'param' => $descriptor));

		if(!$descriptor->trashed()){
			//no action required because is not a trashed document
			return true;
		}
		
		$descriptor->status = DocumentDescriptor::STATUS_PENDING;

		$descriptor->restore();

		$descriptor->save();

		$file = File::onlyTrashed()->findOrFail($descriptor->file_id);

		$file->restore();

		$file->save();


		try{

			$returned_descriptor = $this->reindexDocument($descriptor);

			\Log::info('Core restoreDocument', array('context' => 'DocumentsService', 'response' => $returned_descriptor));

			\Cache::flush();

		}catch(\KlinkException $kex){

			if($kex->getCode() != 404){

				$descriptor->status = DocumentDescriptor::STATUS_ERROR;

				$descriptor->delete();

				\Log::error('Error restoring document from trash', array('context' => 'DocumentsService', 'param' => $descriptor, 'exception' => $kex));

				throw $kex;
			}
			
		}

		return true;
	}
	
	/**
	 * Get a user trashed content.
	 *
	 * Rules: 
	 * - Admin gets everything
	 * - Project Admin gets personal trashed docs/collection + trashed docs and collections in projects they manage
	 * - others get personal trashed documents
	 *
	 * @param  User           $user       The user
	 * @param  int         	  $page       The page of the trash to be returned (in case of multipage return)
	 * @return TrashContentResponse                     The content of the trash
	 */
	public function getUserTrash($user, $page = 1){
		
		$trashed_documents = DocumentDescriptor::onlyTrashed();
		
		$user_is_dms_manager = $user->isDMSManager();
		
		if(!$user_is_dms_manager){
			// if I'm not the Admin I can see only my deleted documents
			$trashed_documents = $trashed_documents->ofUser($user->id);
		}
		
		// TODO: include documents from any users, but are in a Project if the user is a project manager

		// $all = $trashed_documents->get();

		$trashed_collections = new Collection;
		
		// get personal trashed collections
		if($user->can_capability(Capability::MANAGE_OWN_GROUPS)){
			$private_trashed = Group::onlyTrashed()->private($user->id)->get();
			$trashed_collections = $trashed_collections->merge($private_trashed);
		}
		
		
		// Get the project collections w.r.t. user capabilities
		if($user_is_dms_manager){
			// all possible project collection
			$public_trashed = Group::onlyTrashed()->public()->get();
			$trashed_collections = $trashed_collections->merge($public_trashed);
			
		}
		else if($user->isProjectManager()){
			
			$projects = Project::with('collection');
			
			if(!$user_is_dms_manager){
				$projects = $projects->managedBy($user->id);
			}
			
			$projects = Project::with(array('collection'))->managedBy($user->id)->get();
			
			if($projects->count() > 0){
				
				$closure_table = Group::getClosureTable();
				
				$project_collections_ids = $projects->pluck('collection.id')->toArray();
				
				// get the collection descendants that has the project collection as ancestor
				$descendants = \DB::table($closure_table->getTable())->
					whereIn($closure_table->getAncestorColumn(), $project_collections_ids)->
					whereNotIn($closure_table->getDescendantColumn(), $project_collections_ids)->get(array($closure_table->getDescendantColumn()));
				
				$descendants_array = array_pluck($descendants, $closure_table->getDescendantColumn()); 
				
				$project_trashed_collections = Group::onlyTrashed()->public()->whereIn('id', $descendants_array)->get();
				
				$trashed_collections = $trashed_collections->merge($project_trashed_collections);
			
			}
		}
		else if($user->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS)){
			// only show groups that he/she has access
		}

		$paginator_instance = null;
		
		return new TrashContentResponse($trashed_documents->get(), $trashed_collections, $paginator_instance);
	}
    
    
    /**
     * Retrieves all the collections accessible by the specified User
     *
     * @param User $user the user, of course 
     * @return object with personal and projects fields, each will be the {@see Collection} of accessible collections for the specific type
     */
    public function getCollectionsAccessibleByUser( User $user ){
        
        $can_personal = $user->can_capability(Capability::MANAGE_OWN_GROUPS);
            
        $can_see_private = $user->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS);

        $private_groups = null;
        $personal_groups = null;

        if($can_see_private){

            if($user->isDMSManager()){
                $private_groups = \Cache::remember('dms_project_collections', 60, function() {
                
                    return Group::getProjectsTree();
                });
            }
            else {
                
                $private_groups = \Cache::remember('dms_project_collections-' . $user->id, 60, function() use($user) {
                    
                        $managed_private_groups = null;
                        
                        if($user->isProjectManager()){    
                        
                            $roots_project_of_user = $user->managedProjects()->with('collection')->get()->pluck('collection.id')->all();
                        
                            $managed_private_groups = Group::getProjectsTree()->filter(function($item) use($roots_project_of_user) {
                                
                                return in_array($item->id, $roots_project_of_user);  
                            });
                        }
                        
                        $roots_project_of_user = $user->projects()->with('collection')->get()->pluck('collection.id')->all();
                    
                        $private_groups = Group::getProjectsTree()->filter(function($item) use($roots_project_of_user) {
                            
                            return in_array($item->id, $roots_project_of_user);  
                        });
                        
                        if(!is_null($managed_private_groups)){
                            $private_groups = $private_groups->merge($managed_private_groups);
                        }
                        
                        return $private_groups;
                });
                
            }
        }

        if($can_personal){
            $personal_groups = \Cache::remember('dms_personal_collections'.$user->id, 60, function() use($user) {
                return Group::getPersonalTree($user->id);
            });
        }

        
        $r = new \stdClass;
        $r->personal = !is_null($personal_groups) ? $personal_groups : Collection::make([]);
        $r->projects = !is_null($private_groups) ? $private_groups : Collection::make([]);
        return $r;
    }
    
    /**
     * Check if the specified user can access/see a collection
     *
     * @param User $user the user, of course 
     * @param Group $group the parent collection that contains all the child I want 
     * @return bool true if access is granted, false otherwise
     */
    public function isCollectionAccessible( User $user, Group $group ){
        
        // var_dump('calling isCollectionAccessible ' . var_export($user->isDMSManager(), true));
        
        if( $user->isDMSManager() ){
            // admin can do everything
            return true;
        }
        
        // check if is personal
        $personal = $group->is_private && $group->user_id === $user->id && $user->can_capability(Capability::MANAGE_OWN_GROUPS);
        
        // check if is in a project accessible by the user
        
        if($user->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS)){
            $project_collections_ids = $user->projects()->with('collection')->get()->merge($user->managedProjects()->with('collection')->get())->pluck('collection.id')->all();
        }
        else {
            $project_collections_ids = $user->projects()->with('collection')->get()->pluck('collection.id')->all();
        }
        
        $project = !$group->is_private;
        
        if(is_null($group->parent_id)){
            $project = $project && in_array($group->id, $project_collections_ids);
        }
        else {
            $ancestors = $group->getAncestors()->pluck('id')->all();
            
            if(is_array($ancestors)){
                $project = $project && !empty(array_intersect($ancestors, $project_collections_ids));
            }
            else {
                $project = $project && in_array($ancestors, $project_collections_ids);
            }

        } 
        
        // check if is shared with the user
        $shared = Shared::sharedWithMe($user)->where('shareable_id', $group->id)->where('shareable_type', get_class($group))->count() > 0;
        
        // var_dump('User '. $user->id .' Collection '. $group->id .' ' . $group->name . ' = ' . var_export(compact('personal', 'project', 'shared'), true) );
        
        return $personal || $project || $shared;
    }
    
    /**
     * Retrieves all the collections accessible by the specified User, that are descendant of the specified collection
     *
     * @param User $user the user, of course 
     * @param Group $group the parent collection that contains all the child I want 
     * @return Group[] The collections that are accessible by the $user from the specified $group
     */
    public function getCollectionsAccessibleByUserFrom( User $user, Group $group ){
        
        try{
        
            return \Cache::remember('collections-of-'.$user->id.'-from-' . $group->id, 60, function() use($user, $group) {
                
                $collections = Collection::make();
                
                if( !$this->isCollectionAccessible( $user,  $group ) ){
					throw new ForbiddenException( trans('groups.add_documents.forbidden', ['name' => $group->name]), 1);
                }
            
                $collection_ids = [ $group->id ];
                
                $closure_table = Group::getClosureTable();
                    $descendants = \DB::table($closure_table->getTable())->
                                whereIn($closure_table->getAncestorColumn(), $collection_ids)->
                                whereNotIn($closure_table->getDescendantColumn(), $collection_ids)->get(array($closure_table->getDescendantColumn()));
                                
                $descendants_array = array_pluck($descendants, $closure_table->getDescendantColumn());
                    
                $collection_ids = array_merge($collection_ids, $descendants_array); 

                if( $group->is_private ){
                    $instance = new Group;
                    $collections = $instance->private($user->id)->whereIn('id', $collection_ids)->get();
                    
                }
                else {
                    $instance = new Group;
                    $collections = $instance->public()->whereIn('id', $collection_ids)->get();
                }
                
                return $collections;
            
            });
        
        }catch(\Exception $ex){
            \Log::warning('get collections accessible by user '.$user->id.' from ' . $group->id, ['error' => $ex]);
            return Collection::make();
        }
        
    }
    

	/**
	 * Get the document collections in which the document has been added and to which the user has access to.
	 * In other words: private collections, project collections that are under a project managed by the user or on which the user has been added to.
	 *
	 * @param DocumentDescriptor $document the document to get the collections for
	 * @param User $user the user, of course
	 * @return Groups[] the collection of groups in which the $document is categorized 
	 */
	public function getDocumentCollections(DocumentDescriptor $document, User $user){
		
		// get private collections of the user
		$private_groups = $document->groups()->private($user->id)->get();
		
		// get project collections accessible wrt user profile
		$public_groups = $document->groups()->public();
		
		if( !$user->isDMSManager() ){

			$project_collections_ids_method1 = $user->projects()->with('collection')->get()->merge($user->managedProjects()->with('collection')->get())->pluck('collection.id')->all();
		
			// all descendants of $projects
			$closure_table = Group::getClosureTable();
			$descendants = \DB::table($closure_table->getTable())->
						whereIn($closure_table->getAncestorColumn(), $project_collections_ids_method1)->
						whereNotIn($closure_table->getDescendantColumn(), $project_collections_ids_method1)->get(array($closure_table->getDescendantColumn()));
						
			$descendants_array = array_pluck($descendants, $closure_table->getDescendantColumn());
			
			$project_collections_ids = array_merge($project_collections_ids_method1, $descendants_array);

			$public_groups = $public_groups->whereIn('groups.id', $project_collections_ids);
		}
		
		return $private_groups->merge($public_groups->get());
		
	}

	// --- Groups related functions ----------------------------

	
	// group operation are only available for private descriptor so only private descriptors are touched by operation on groups

	/**
	 * Create a document group.
	 * @param  User           $user       The user that is creating the group
	 * @param  string         $name       The name to assign to the group
	 * @param  string         $color      The color to assign to the group (hex color without the initial #)
	 * @param  Group|null     $parent     The parent Group if any
	 * @param  boolean        $is_private If the group is user private or institution visible (default true)
	 * @param  GroupType|null $type       The type of the group, default @see GroupType::getGenericType()
	 * @return Group                     The instance of the created Group
	 * @throws ForbiddenException If the user cannot manage own groups or institution groups (if private is set to false)
	 */
	public function createGroup(User $user, $name, $color=null, Group $parent = null, $is_private = true, GroupType $type = null)
	{

		if(!$user->can_capability(Capability::MANAGE_OWN_GROUPS) || (!$is_private && !$user->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS)) ){
			throw new ForbiddenException("Permission denieded for performing the group creation.");
		}

		if(is_null($type)){
			$type = GroupType::getGenericType();
		}

		$already_exists = $this->checkIfGroupExists($user, $name, $parent, $is_private);

		if($already_exists){
			throw new GroupAlreadyExistsException($name, $parent);
			
		}


		$new_group = Group::create(array(
			'user_id' => $user->id,
			'name' => $name,
			'color' => $is_private ? '16a085' : 'f1c40f',
			'group_type_id' => $type->id,
			'is_private' => $is_private
			));


		if(!is_null($parent)){

			$parent->addChild($new_group);

		}
		else {
			$new_group->makeRoot(0);
		}

		return $new_group;
	}


	public function checkIfGroupExists(User $user, $name, Group $parent = null, $is_private = true)
	{

		if(is_null($parent)){

			// user_id 'name', 'parent_id'

			$source = Group::getRoots();

		}
		else {

			$source = $parent->getChildren();

		}


		return !$source->where('user_id', $user->id)->where('name', $name)->isEmpty();

	}

	/**
	 * Creates an instition level group given a folder full path.
	 *
	 * Supports only Linux Path separ
	 */
	public function createGroupsFromFolderPath(User $user, $folder, $merge = true, $make_private = false, Group $parent = null)
	{
		
		$that = $this;

		$type = GroupType::getFolderType();
        
        $folder = str_replace('\\', '/', $folder);

		return \DB::transaction(function() use($user, $folder, $merge, $type, $make_private, $parent)
		{

			$paths = array_values(array_filter(explode('/', $folder)));

			$count_paths = count($paths);

			$exists = false;
			$search = null;
			$group_collection = (!is_null($parent)) ? $parent->getChildren() : Group::getRoots();
			$parent_group = (!is_null($parent)) ? $parent : null;

			for ($i=0; $i < $count_paths; $i++) { 
			
				$dir = $paths[$i];
				
				$search = $group_collection->where('name', $dir);

				$exists = $search->count() > 0;

				if(!$exists){

					$parent_group = $this->createGroup($user, $dir, 'f1c40f', $parent_group, $make_private, $type);
					$group_collection = $parent_group->getChildren();

				}
				else {
					$parent_group = $search->first();
					$group_collection = $parent_group->getChildren();
				}

			}


			return $parent_group;

		});
	}

	/**
	 * Update the group details (used for renaming and change color)
	 */
	public function updateGroup(User $user, Group $group, array $details)
	{

		if(!$group->is_private && !$user->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS) ){
			throw new ForbiddenException("You cannot edit project collections.");
		}

		if(!$user->can_capability(Capability::MANAGE_OWN_GROUPS) && $group->user_id != $auth->user()->id ){
			throw new ForbiddenException("You cannot edit someone else collection.");
		}

		$hasSiblings = $group->hasSiblings();

		if(isset($details['name']) && !empty($details['name']) && $hasSiblings){
			//check if rename is safe

			$group_collection = $group->isRoot() ? Group::getRoots() : $group->getParent()->getChildren();

			$is_there_already = !$group_collection->where('name', e($details['name']))->where('user_id', $user->id)->isEmpty();

			if($is_there_already) {
				throw new ForbiddenException("A group with same name already exists. You cannot rename ". $group->name .".", 11);
			}
		}


		$that = $this;
		return \DB::transaction(function() use($user, $group, $details)
		{

			if(isset($details['name']) && !empty($details['name'])){

				$new_name = e($details['name']);

				$group->name = e($details['name']);

			}

			if(isset($details['color']) && !empty($details['color'])){

				$group->color = $details['color'];

			}

			if($group->isDirty()){

				$group->save();				
			}

			return $group;

		});
	}
	
	/**
	 * Delete a Group.
	 * The group and all the descendant are marked as delete and the documents contained are only removed from the groups (not deleted)
	 */
	public function deleteGroup(User $user, Group $group)
	{

		if(!$group->is_private && !$user->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS)){
			throw new ForbiddenException(trans('groups.delete.forbidden_delete_project_collection', ['collection' => $group->name]));
		}

		if($group->is_private && $user->id != $group->user_id && !$user->can_capability(Capability::MANAGE_OWN_GROUPS)){
			throw new ForbiddenException(trans('groups.delete.forbidden_delete_collection', ['collection' => $group->name]));
		}

		// TODO: if the collection was shared and the user that is deleting is the target of the share?

		$that = $this;
		$retval = \DB::transaction(function() use($user, $group, $that)
		{


			// remove all documents from the group
			// reindex all the documents

			$that->removeDocumentsFromGroup($user, $group->documents, $group);

			if($group->hasDescendants()){

				$descendants = $group->getDescendants();

				foreach ($descendants as $descendant) {

					if($descendant->user_id == $user->id){

						$that->removeDocumentsFromGroup($user, $descendant->documents, $descendant);

						$descendant->delete();
					}
					
				}

			}

			// mark the group as deleted
			return $group->delete();

		});
		
		\Cache::forget('dms_personal_collections' . $user->id);
		
		return $retval;
	}

	/**
	 * Move an existing group under another existing group. If the group to move under ($moveBelow) is set to null the group will be moved as new root
	 */
	public function moveGroup(User $user, Group $group, Group $moveBelow = null, $merge = false)
	{
		$that = $this;
		return \DB::transaction(function() use($user, $group, $moveBelow, $merge, $that)
		{

			// base, move a leaf or a child in a totally new place (no-existing group with same details exists)

			$group_collection = is_null($moveBelow) ? Group::getRoots() : $moveBelow->getChildren();

			$is_there_already = !$group_collection->where('name', $group->name)->where('user_id', $user->id)->isEmpty();

			if($is_there_already && !$merge) {
				throw new ForbiddenException("A group with same name already exists. Please select merge option if you want to proceed.", 10);
			}


			// move to a root and no existing root with same name

			if(!$is_there_already && is_null($moveBelow)){
				// no ther roots have the same name

				$group->makeRoot(0);

				return $group->fresh();
			}

			// move under a parent and no other child has the same name as the group I'm moving
			else if(!$is_there_already && !is_null($moveBelow)){

				$group->moveTo(0, $moveBelow);

				return $group->fresh();

			}

			//already existing group
			else if($is_there_already){

				//need to merge the group at current level -> add the moved group documents to the existing group

				$existing_group = $group_collection->where('name', $group->name)->where('user_id', $user->id)->first();

				$moving_documents = $group->documents;

				// remove the old group from the moved group documents

				$that->moveDocumentsToGroup($user, $moving_documents, $existing_group, $group); // from $group to $moveBelow

				// do the same for each child

				if($group->hasChildren()){

					foreach ($group->getChildren() as $child) {
						$that->moveGroup($user, $child, $existing_group, $merge);
					}

				}

				// delete the old group

				$group->delete();

				return $existing_group->fresh();
			}


			throw new \Exception("Move is not possible.", 1);
			

		}); //end transaction

	}

	/**
	 * Copy an existing group in another position of the hierarchy. For the final position if no parent is specified the group will be copyied as a root
	 * Please use the $merge parameter in case the group tree under the new position is already existing
	 */
	public function copyGroup(User $user, Group $group, Group $copyUnder = null, $merge = false)
	{
		$that = $this;
		return \DB::transaction(function() use($user, $group, $copyUnder, $merge, $that)
		{

			$group_collection = is_null($copyUnder) ? Group::getRoots() : $copyUnder->getChildren();

			$is_there_already = !$group_collection->where('name', $group->name)->where('user_id', $user->id)->isEmpty();

			// $is_there_already = !is_null($existing_group) && $existing_group->user_id == $user->id; // existing group for same user? (this will break the uniqueness constraint on db if exists)

			if($is_there_already && !$merge) {
				throw new ForbiddenException("A group with same name already exists. Please select merge option if you want to proceed.", 10);
			}

			// copy to a root and no existing root with same name

			if(!$is_there_already && is_null($copyUnder)){
				// no ther roots have the same name

				$copied = $that->createGroup($user, $group->name, $group->color, null, $group->is_private);

				$that->addDocumentsToGroup($user, $group->documents, $copied);

				if($group->hasChildren()){

					foreach ($group->getChildren() as $child) {
						$that->copyGroup($user, $child, $copied, $merge);
					}

				}

				return $copied;
			}

			// move under a parent and no other child has the same name as the group I'm moving
			else if(!$is_there_already && !is_null($copyUnder)){

				$copied = $that->createGroup($user, $group->name, $group->color, $copyUnder, $group->is_private);

				$that->addDocumentsToGroup($user, $group->documents, $copied);

				if($group->hasChildren()){

					foreach ($group->getChildren() as $child) {
						$that->copyGroup($user, $child, $copied, $merge);
					}

				}

				return $copied;

			}

			// at least there is already the source node in the destination sub-tree
			else if($is_there_already){

				$existing_group = $group_collection->where('name', $group->name)->where('user_id', $user->id)->first();

				$this->addDocumentsToGroup($user, $group->documents, $existing_group);

				if($group->hasChildren()){

					foreach ($group->getChildren() as $child) {
						$that->copyGroup($user, $child, $existing_group, $merge);
					}

				}

				return $existing_group->fresh();

			}

			throw new \Exception("Copy is not possible.", 1);


		});

		// return true;
	}

	/**
	 * Test if a copy or move group can be performed
	 */
	public function canCopyOrMoveGroup(User $user, Group $group, Group $under = null)
	{
		$group_collection = is_null($under) ? Group::getRoots() : $under->getChildren();

		$is_there_already = !$group_collection->where('name', $group->name)->where('user_id', $user->id)->isEmpty();

		// $is_there_already = !is_null($existing_group) && $existing_group->user_id == $user->id; // existing group for same user? (this will break the uniqueness constraint on db if exists)

		if($is_there_already) {
			return false;
		}


		return true;
	}

	/**
	 * Make the group institution visible
	 */
	public function makeGroupPublic(User $user, Group $group)
	{

		if(!$group->is_private){
			return true;
		}
		
		if(!$user->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS) ){
			throw new ForbiddenException("Permission denieded for making the collection a project collection.");
		}

		// change flag
		$group->is_private = false;
		$group->color = 'f1c40f';
		$group->save();
		// reindex all the documents in that group

		$this->reindexDocuments($group->documents);

		$group->getDescendants()->each(function($c){
			
			$c->is_private = false;
			$c->color = 'f1c40f';
			$c->save();

			$this->reindexDocuments($c->documents);
		
		});

		// TODO: make public also the sub-collections
		
		\Cache::forget('dms_personal_collections' . $user->id);

		
		
		

		return true;
	}

	/**
	 * Make the group private and no more institution visible
	 */
	public function makeGroupPrivate(User $user, Group $group)
	{

		if($group->is_private){
			return true;
		}

		if(!$user->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS) ){
			throw new ForbiddenException("Permission denieded for making the project collection a personal collection.");
		}
		
		// change flag
		$group->is_private = true;
		$group->color = '16a085';
		$group->save();

		$this->reindexDocuments($group->documents);

		$group->getDescendants()->each(function($c){
			
			$c->is_private = true;
			$c->color = '16a085';
			$c->save();

			$this->reindexDocuments($c->documents);
		
		});
		
		\Cache::forget('dms_personal_collections' . $user->id);


		
		// reindex all the documents in that group

		return true;
	}

	/**
	 * Move the documents from $origin Group to $destination Group
	 */
	public function moveDocumentsToGroup(User $user, Collection $documents, Group $origin, Group $destination)
	{
		# transaction
		# 1. add documents to new group
		# 2. remove documents from old group
		# 3. when removing perform a reindex

		//$new_documents = $group->documents;

		

		$this->addDocumentsToGroup($user, $documents, $destination, false);

		$this->removeDocumentsFromGroup($user, $documents, $origin, true);

	}

	/**
	 * Add documents to a group.
     *
     * This method don't check if documents are already added to the group
     *
     * @param User $user the user that is performing the operation
     * @param DocumentDescriptor[] $documents the document descriptors to be added to the $group
     * @param Group $group the group to be added to the documents
     * @param bool $perform_reindex if the document reindex operation must be performed, default true
	 */
	public function addDocumentsToGroup(User $user, Collection $documents, Group $group /*, $docTitles = null*/, $perform_reindex = true)
	{
		
		if( !$this->isCollectionAccessible($user, $group) ){
			
			throw new ForbiddenException( trans('groups.add_documents.forbidden', ['name' => $group->name]), 1);
			
		}
        
        // TODO: filter $documents already in that group

		$group->documents()->saveMany($documents->all()); // documents must be a collection of DocumentDescriptors

		if($perform_reindex){
			$this->reindexDocuments($documents); //documents must be a collection of DocumentDescriptors
		}
	}

	public function addDocumentToGroup(User $user, DocumentDescriptor $document, Group $group /*, $docTitles = null*/, $perform_reindex = true)
	{
		if( !$this->isCollectionAccessible($user, $group) ){
			
			throw new ForbiddenException( trans('groups.add_documents.forbidden', ['name' => $group->name]), 1);
			
		}

		$group->documents()->save($document);

		if($perform_reindex){
			$this->reindexDocument($document, 'private');
		}
	}

	/**
	 * remove documents from a group
	 */
	public function removeDocumentsFromGroup(User $user, Collection $documents, Group $group, $perform_reindex = true)
	{

		if( !$this->isCollectionAccessible($user, $group) ){
			
			throw new ForbiddenException( trans('groups.remove_documents.forbidden', ['name' => $group->name]), 1);
			
		}

		// $documents is integer, is DocumentDescriptor, is array of ints, is Collection and contains DocumentDescriptor

		$collection_of_ids = $group->documents->pluck('id')->toArray();

		//get the docs, detach, reindex the docs

		$group->documents()->detach($collection_of_ids); // $documents must be an integer or an array of integers

		if($perform_reindex){
			$this->reindexDocuments($documents);  //documents must be a collection of DocumentDescriptors
		}
	}

	public function removeDocumentFromGroup(User $user, DocumentDescriptor $document, Group $group, $perform_reindex = true)
	{

		if( !$this->isCollectionAccessible($user, $group) ){
			
			throw new ForbiddenException( trans('groups.remove_documents.forbidden', ['name' => $group->name]), 1);
			
		}

		$group->documents()->detach($document);

		if($perform_reindex){
			$this->reindexDocument($document, 'private');
		}
	}


	


	// --- Import related functions ----------------------------


	public function importStatus(User $user)
	{
		$imports_completed = Import::completed($user->id)->with('file')->get();

        $imports_progress = Import::notCompleted($user->id)->with('file')->get();
        
		$imports_error = Import::withError($user->id)->get();

        $only_progress = $imports_progress->count();

        $only_completed = $imports_completed->count();
        
		$only_error = $imports_error->count();

        $total = $only_completed + $only_progress;
		
        $resp = array(
            'status' => array(
                'global' => trans_choice('import.import_status_general', $only_progress, ['num' => $only_progress]), // > 0 ? "$only_progress import in progress" : 'Import completed',
                'details' => trans('import.import_status_details', ['total' => $total, 'completed'=> $only_completed, 'executing' => $only_progress]) , 
                'progress_percentage' => $total > 0 ? round($only_completed/$total*100)."%" : "0%",
            ),
            'imports' => $imports_progress->merge($imports_completed),
            'imports_total' => $total,
            'imports_completed' => $only_completed + $only_error,
            'imports_progress' => $only_progress,
        );

        return $resp;
	}

	/**
	 * Enqueue imports from url.
	 * 
	 * Before that checks is a file from the same origin is already in the system, no matter who uploaded it.
	 * 
	 * A "Fail First" approach is followed. All security checks are performed before the actual async job will be enqueued so if an exception is thrown is 
	 * guaranteed that all the input has been rejected (no partial import to handle) 
	 * 
	 * @throws FileAlreadyExistsException if a file from the same origin is already existing. The all procedure of import will be blocked and no file will be enqueued
	 * @throws InvalidArgumentException if the @see $url parameter is not a valid url or is an empty string. Also in this case the whole procedure is stopped
	 * 
	 * @param string $urls the url list as one url for line, line ending could be ; of new line
	 * @param \KlinkDMS\User $uploader the user that has started the import process
	 * @return array with 'count' and 'messages' keys with th number of added jobs
	 */
	public function importFromUrl($urls, User $uploader)
	{

		// extraction and validity check

		$urls = preg_split( "/[;\n\r]+/", $urls );

		$urls = array_map(function($el){ return str_replace(' ', '%20', $el); }, $urls);

		foreach ($urls as $url) {
			if($this->fileExistsFromOriginalUrl($url)){

				$f = File::fromOriginalUri($url)->first();

				$descr = $f->getLastVersion()->document;

				throw new FileAlreadyExistsException($url, $descr, $f);
			}

			\KlinkHelpers::is_valid_url($url);
		}

		//ok, now it's time to import (aka enqueue)
        
        $temp_hash = 'aaa';

		foreach ($urls as $url) {
            
            $temp_hash = md5($url);

			$filename = $this->extractFileNameFromUrl($url);

			$file = new File();
	        $file->name= $filename; // could be edited during the async process
	        $file->hash=$temp_hash; // edited during the async process
	        $file->mime_type=''; // edited during the async process
	        $file->size= 0; // edited during the async process
	        $file->revision_of=null;
	        $file->thumbnail_path=null;

            $file->path = $this->constructLocalPathForImport($filename, $temp_hash);
            
            $file->user_id = $uploader->id;
            $file->original_uri = $url;
            $file->is_folder = false;//if remote, the root should not be a directory
            $file->save();

            $import = new Import();
            $import->bytes_expected = 0;
            $import->bytes_received = 0;
            $import->is_remote = true;
            $import->file_id = $file->id;
            $import->status = Import::STATUS_QUEUED;
            $import->user_id = $uploader->id;
            $import->parent_id = null;
            $import->status_message = Import::MESSAGE_QUEUED;
            $import->save();
			
			Queue::push(new ImportCommand($uploader, $import));

        }

        $count = count($urls);

        return array(
        	'count' => $count,
        	'message' => $count . ' web address added to the import queue'
        );
	}

	/**
	 * Enqueue imports from folder (local or shared).
	 * 
	 * Before that checks is a file from the same origin is already in the system, no matter who uploaded it.
	 * 
	 * A "Fail First" approach is followed. All security checks are performed before the actual async job will be enqueued so if an exception is thrown is 
	 * guaranteed that all the input has been rejected (no partial import to handle) 
	 * 
	 * @throws FileAlreadyExistsException if a file from the same origin is already existing. The all procedure of import will be blocked and no file will be enqueued
	 * @throws InvalidArgumentException if the @see $paths parameter contains invalid paths or is an empty string. Also in this case the whole procedure is stopped
	 * 
	 * @param string $paths the paths list as one path for line, line separator could be ; of new line
	 * @param \KlinkDMS\User $uploader the user that has started the import process
	 * 
	 * @param boolean $copy set to true will perform the copy of all files from the original folder to the DMS documents folder, set to false if the file don't need to be copied but only inserted into the db (default true)
	 * 
	 * @return array with 'count' and 'messages' keys with th number of added jobs
	 */
	public function importFromFolder($paths, User $uploader, $copy = true, $recursive = true)
	{
		// $paths = preg_split( "/[;\n\r]+/", $paths );

		if(!$copy){
			throw new \NotImplementedException("The NO-COPY, but sync version is not yet ready", 23000);
		}

		if(!$recursive){
			throw new \NotImplementedException("The NO-RECURSION option is not available in this build", 42000);
		}


		// get the common ancestor to all the paths so we have starting path for folder names (and for recreating folder structure)

		$root_folder_name = basename($paths);

		$root_folder_name_pos = strrpos($paths, $root_folder_name);

		$root_folder_path = substr($paths, 0, $root_folder_name_pos);

		// when you will realize why this is the right code you'll became mad (note the little r in strpos) (of course can be optimized, but if you think that you haven't got the point)


		// ok let's grab all the sub folders

		$dirs = [$paths];

		$subdirs = $this->directories($paths);

		$dirs = array_merge($dirs, $subdirs);

		\Log::info('importFromFolder', array('context' => 'DocumentsService', 'dirs' => $dirs));


		foreach ($dirs as $directory) {

			$filename = str_replace('\\', '/', substr($directory, $root_folder_name_pos));

			// \Log::info('importFromFolder', array('context' => 'DocumentsService', 'filename' => $filename, 'path' => $this->constructLocalPathForFolderImport($filename)));

			$file = new File();
	        $file->name= $filename; // the directory name
	        $file->hash=md5($directory); // temp for the directory
	        $file->mime_type=''; 
	        $file->size= 0; // directory size is unknown and will not be calculated
	        $file->revision_of=null;
	        $file->thumbnail_path=null;

            $file->path = $this->constructLocalPathForFolderImport($filename);
            
            $file->user_id = $uploader->id;
            $file->original_uri = $directory;
            $file->is_folder = true;
            $file->save();

            $import = new Import();
            $import->bytes_expected = 0;
            $import->bytes_received = 0; // will be updated by the queue
            $import->is_remote = false;
            $import->file_id = $file->id;
            $import->status = Import::STATUS_QUEUED;
            $import->user_id = $uploader->id;
            $import->parent_id = null;
            $import->status_message = Import::MESSAGE_QUEUED;
            $import->save();

            // only folders will be enqueued, the files in that folders will be grabbed during the async import

            // create the corresponding group

            $group = $this->createGroupsFromFolderPath($uploader, str_replace(realpath(Config::get('dms.upload_folder')), '', $file->path), true);
            
			Queue::push(new ImportCommand($uploader, $import, $group, $copy));

        }

        $count = count($dirs);

        return array(
        	'count' => $count,
        	'message' => $count . ' folders added to the import queue'
        );

	}


	/**
	 * Handle the "import" operation needed for and uploaded file using a form based approach
	 * 
	 * 
	 * @return DocumentDescriptor|array a DocumentDescriptor instance in case of full success, an array with keys 'descriptor' and 'error' in case of an indexing error, but the DocumentDescriptor has been saved correctly
	 */
	public function importFile(UploadedFile $upload, User $uploader, $visibility = 'private', Group $group = null)
	{

		try{

			$file = $this->constructLocalPathForImport($upload->getClientOriginalName());

			$filename = basename($file);

			$destination_dir = dirname($file);

			$file_from_storage_path = str_replace(Config::get('dms.upload_folder'), '', $file);

	             
			$file_m_time = false; //$upload->getMTime(); // will work?

			// move the file from the temp upload dir to the final position
			$new_file = $upload->move($destination_dir, $filename);

			$hash = \KlinkDocumentUtils::generateDocumentHash($file);



			if(DocumentDescriptor::existsByHash($hash)){

				unlink($file);
				
				throw new FileAlreadyExistsException($upload->getClientOriginalName(), DocumentDescriptor::findByHash($hash));

			}
			
			if(File::existsByHash($hash)){

				unlink($file);

				// get the document from the file
				$file_model = File::findByHash($hash);
				$existing_descriptor = $file_model->getLastVersion()->document;
				
				throw new FileAlreadyExistsException($upload->getClientOriginalName(), $existing_descriptor, $file_model);

				
			}

			if(!$this->verifyNamingPolicy($filename)){

				unlink($file);
				
				throw new FileNamingException( trans('errors.upload.filenamepolicy', ['filename' => $upload->getClientOriginalName()]), 2);
				
			}

			

            $mime = \KlinkDocumentUtils::get_mime($file);

            $file_model = new File();
            $file_model->name= $filename;
            $file_model->hash=$hash;
            $file_model->mime_type=$mime; 
            $file_model->size= Storage::size($file_from_storage_path);
            $file_model->thumbnail_path=null;
            $file_model->path = $file;
            $file_model->user_id = $uploader->id;
            $file_model->original_uri = $file;
            $file_model->is_folder = false;
            
            if($file_m_time){
                $file_model->created_at = \Carbon\Carbon::createFromFormat('U', $file_m_time);
            }
            $file_model->save();

            $file_model = $file_model->fresh();

            try{

                $descriptor = $this->indexDocument( $file_model, $visibility, $uploader, $group, true ); //TODO: pass also the group info to the indexDocument function
				
				dispatch(new ThumbnailGenerationJob($file_model));

                // if(is_array($descriptor)){
                	//something bad happened during indexing, but the descriptor is saved on the db
                // }

                return $descriptor;

            } catch(\KlinkException $kex){
                // if cannot be indexed is not a real problem here thanks to the status of the DocumentDescriptor everyhting can be solved
                // at a later time
                \Log::error('Indexing during import exception', array('context' => 'DocumentsService@importFile', 'exception' => $kex, 'file' => $file_model->toArray() ));
            } catch(\Exception $kex){
                // if cannot be indexed is not a real problem here thanks to the status of the DocumentDescriptor everyhting can be solved
                // at a later time
                \Log::error('Indexing during import exception', array('context' => 'DocumentsService@importFile', 'exception' => $kex, 'file' => $file_model->toArray() ));
            }
            

        }
        catch(\Exception $ex) {


            \Log::error('File copy error', array('context' => 'DocumentsService@importFile', 'upload' => $upload->getClientOriginalName(), 'owner' => $uploader->id, 'error' => $ex));


            throw $ex;
            
        
        }

        


        
	}

	/**
	 * Construct a File from an Uploaded File. Import the file in the correct folder and perform all the checks
	 *
	 * @return File
	 */
	public function createFileFromUpload(UploadedFile $upload, User $uploader, File $revision_of = null){

		try{

			$file = $this->constructLocalPathForImport($upload->getClientOriginalName());

			$filename = basename($file);

			$destination_dir = dirname($file);

			$file_from_storage_path = str_replace(Config::get('dms.upload_folder'), '', $file);

	             
			$file_m_time = false; //$upload->getMTime(); // will work?

			// move the file from the temp upload dir to the final position
			$new_file = $upload->move($destination_dir, $filename);

			$hash = \KlinkDocumentUtils::generateDocumentHash($file);

			if(File::existsByHash($hash)){

				unlink($file);

				$f = File::findByHash($hash);

				$descr = $f->getLastVersion()->document;
				
				throw new FileAlreadyExistsException($filename, $descr, $f);
				
			}

			if(!$this->verifyNamingPolicy($filename)){

				unlink($file);
				
				throw new FileNamingException( trans('errors.upload.filenamepolicy', ['filename' => $upload->getClientOriginalName()]), 2);
				
			}

			

            $mime = \KlinkDocumentUtils::get_mime($file);

            $file_model = new File();
            $file_model->name= $filename;
            $file_model->hash=$hash;
            $file_model->mime_type=$mime; 
            $file_model->size= Storage::size($file_from_storage_path);
            $file_model->thumbnail_path=null;
            $file_model->path = $file;
            $file_model->user_id = $uploader->id;
            $file_model->original_uri = $file;
            $file_model->is_folder = false;
            
            if($file_m_time){
                $file_model->created_at = \Carbon\Carbon::createFromFormat('U', $file_m_time);
            }

            if(!is_null($revision_of)){
            	$file_model->revision_of = $revision_of->id;
            }

            $file_model->save();



            return $file_model;
        }
        catch(\Exception $ex) {


            \Log::error('File copy error', array('context' => 'DocumentsService@createFileFromUpload', 'upload' => $upload->getClientOriginalName(), 'owner' => $uploader->id, 'error' => $ex));


            throw $ex;
            
        
        }
	}


	public function constructUrl($id, $type = 'document')
	{

		if (app()->runningInConsole())
		{
		    return app('Illuminate\Contracts\Routing\UrlGenerator')->to('dms/klink', array($id, $type), \Config::get('dms.use_https', false));
		}

		return app('Illuminate\Contracts\Routing\UrlGenerator')->to('klink', array($id, $type), \Config::get('dms.use_https', false));

	}



	/**
	 * Return the path where the file that will be imported will be saved.
	 * 
	 * Performs:
	 * - file name sanitation
	 * - create the containing folder if not already there
	 * - return the absolute path
	 * 
	 * The folder will be constructed according to the YEAR/MONTH policy
	 * 
	 * @param string $original_filename The name of the file that will be saved on disk and needs a folder
	 * @return string the absolute path reserved on disk for the filename
	 */
	public function constructLocalPathForImport( $original_filename, $seed = null )
	{
		// sanitize filename

		$filename = $this->sanitize_file_name($original_filename);
        
        if( !is_null($seed) && !empty($seed) ){
            $filename = substr($filename, 0, 50) . '-' . $seed;
        }

		// folder based on YEAR/MONTH

		$year_folder = date('Y');

		$month_folder = date('m');

		$dir = $year_folder . DIRECTORY_SEPARATOR . $month_folder;

		$is_dir = Storage::exists($dir);

		if(!$is_dir){
			// create containing folder
			$is_dir = Storage::makeDirectory($dir, 0755, true);

			if(!$is_dir){
				\Log::error('Cannot create folder ', array('context' => 'DocumentsService', 'param' => $dir));
			}
		}

		if($is_dir){
			$filename = $dir . DIRECTORY_SEPARATOR . $filename;
		}

		if (Storage::exists($filename))
		{
		    // edit with a one or other

		    $extension = pathinfo($filename, PATHINFO_EXTENSION); // Storage::extension($filename);

		    $filename = str_replace('.' . $extension, '', $filename);

		    $filename .= '-' . substr(md5(microtime()), 0, 6) . '.' . $extension;

		}
		
		$abso_path = Config::get('dms.upload_folder') . $filename;

		return /* the absolute path */ $abso_path;
	}

	public function constructLocalPathForFolderImport( $folder_name )
	{
		
		$is_dir = Storage::exists($folder_name);

		if(!$is_dir){
			// create containing folder
			$is_dir = Storage::makeDirectory($folder_name, 0755, true);

			if(!$is_dir){
				\Log::error('Cannot create folder ', array('context' => 'DocumentsService', 'param' => $folder_name));
			}
		}
		
		$abso_path = Config::get('dms.upload_folder') . $folder_name;

		return /* the absolute path */ $abso_path;
	}

	// --- Document Management facilities ----------------------
	

	/**
	 * Returns the number of indexed documents with the respect to the visibility.
	 *
	 * Public visibility -> all documents inside the K-Link Network
	 *
	 * private visibility -> documents inside institution K-Link Core
	 *
	 * This method uses caching, so be aware that the results you receive might be older than real time
	 * 
	 * @param  string $visibility the visibility (if nothing is specified, a 'public' visibility is considered)
	 * @return integer            the amount of documents indexed
	 */
	public function getDocumentsCount($visibility = 'public')
	{
		return $this->adapter->getDocumentsCount($visibility);
	}

	/**
	 * Returns the information needed for rendering the Storage Status widget
	 * @return array
	 */
	public function getStorageStatus($raw = false)
	{

		$document_categories = $this->adapter->getDocumentsStatistics();

		$data = $this->getStorageData($raw);
		
		return array_merge($data, ['document_categories' => $document_categories]);
	}


	public function getStorageData($raw = false){

		$docs_folder = Config::get('dms.upload_folder');

		$app_folder = app_path();

		$free_space = disk_free_space($docs_folder);

		$free_space_app = disk_free_space($app_folder);

		$total_space = disk_total_space($docs_folder);

		$total_space_app = disk_total_space($app_folder);

		$free_space_on_docs_folder = DocumentsService::human_filesize($free_space);

		$free_space_on_app_folder = DocumentsService::human_filesize($free_space_app);

		$total_space_on_docs_folder = DocumentsService::human_filesize($total_space);

		$total_space_on_app_folder = DocumentsService::human_filesize($total_space_app);

		$full_percentage = round(($total_space - $free_space)/$total_space*100);

		$base = compact(
			'docs_folder', 
			'app_folder', 
			'free_space_on_docs_folder', 
			'free_space_on_app_folder',
			'total_space_on_docs_folder',
			'total_space_on_app_folder',
			'full_percentage'
		);

		$raw_data = array(
			'free_app' => $free_space_app, 
			'free_docs' => $free_space,
			'total_app' => $total_space_app, 
			'total_docs' => $total_space,
			'used_app' => $total_space_app - $free_space_app,
			'used_docs' => $total_space - $free_space,
		);

		if($raw){
			$base = array_merge($base, ['raw_data' => $raw_data]);
		}

		return $base;

	}


	/**
	 * Scan the imported and indexed documents for possible duplicates
	 */
	public function searchForDuplicates()
	{
		# code...
	}

	/**
	 * Check if the same file exists
	 */
	public function fileExists(File $value)
	{
		# code...
	}

	/**
	 * Check if file from a same url has been already added
	 * 
	 * @param string $url the url of the file to be checked
	 * @return boolean true if a file from the same url is already in the system, false otherwise
	 */
	public function fileExistsFromOriginalUrl($url)
	{
		return !is_null(File::fromOriginalUri($url)->first());
	}


	/**
	 * Get all of the directories within a given directory.
	 *
	 * @param  string  $directory
	 * @return array
	 */
	public function directories($directory)
	{
		$directories = array();

		foreach (Finder::create()->in($directory)->directories()->ignoreUnreadableDirs() as $dir)
		{
			$directories[] = $dir->getPathname();
		}

		return $directories;
	}


	public function files($directory)
	{
		return iterator_to_array(Finder::create()->files()->in($directory), false);
	}


	/**
	 * Check if the file respects the naming policy
	 * @param  string $filename [description]
	 * @return boolean           [description]
	 */
	public function verifyNamingPolicy($filename)
	{

		$active = Option::option('dms.namingpolicy.active', false);

		if(!$active){
			// the naming policy is not active
			return true;
		}

		return preg_match('/^(\d{4}-\d{2}-\d{2})_([a-zA-Z\-\s]+)_([a-zA-Z\-]+)_([a-zA-Z]{2})_(\d{1,3}).(.*)$/i', $filename);
	}



	/**
	 * Sanitizes a filename, replacing whitespace with dashes.
	 *
	 * Removes special characters that are illegal in filenames on certain
	 * operating systems and special characters requiring special escaping
	 * to manipulate at the command line. Replaces spaces and consecutive
	 * dashes with a single dash. Trims period, dash and underscore from beginning
	 * and end of filename.
	 *
	 * @see Wordpress 4.1.1
	 *
	 * @param string $filename The filename to be sanitized
	 * @return string The sanitized filename
	 */
	public function sanitize_file_name( $filename ) {
	        $filename_raw = $filename;
	        $special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0));
	        
	        $filename = preg_replace( "#\x{00a0}#siu", ' ', $filename );
	        $filename = str_replace( $special_chars, '', $filename );
	        $filename = str_replace( array( '%20', '+' ), '-', $filename );
	        $filename = preg_replace( '/[\r\n\t -]+/', '-', $filename );
	        $filename = trim( $filename, '.-_' );
	
	        // Split the filename into a base and extension[s]
	        $parts = explode('.', $filename);
	
	        // Return if only one extension
	        if ( count( $parts ) <= 2 ) {
		        return $filename;
	        }
	
	        // Process multiple extensions
	        $filename = array_shift($parts);
	        $extension = array_pop($parts);
	        // $mimes = get_allowed_mime_types();
	
	        /*
	         * Loop over any intermediate extensions. Postfix them with a trailing underscore
	         * if they are a 2 - 5 character long alpha string not in the extension whitelist.
	         */
	        foreach ( (array) $parts as $part) {
	                $filename .= '.' . $part;
	
	                if ( preg_match("/^[a-zA-Z]{2,5}\d?$/", $part) ) {
	                    $filename .= '_';
	                }
	        }
	        $filename .= '.' . $extension;
	        
	        return $filename;
	}

	/**
	 * Guess a human understandable file name from a URL.
	 * 
	 * This method don't perform requests to a url to get other metadata
	 * 
	 * @param string $url the URL
	 * @return string The guessed filename
	 * @throws InvalidArgumentException if the given $url is not well formatted
	 */
	public function extractFileNameFromUrl($url)
	{

		\KlinkHelpers::is_valid_url($url);

		$parts = parse_url($url);

		$host = $parts['host'];
		if(isset($parts['path'])){
			$path = basename(urldecode($parts['path']));
		}
		else {
			$path = '';
		}

		$name = '['. $host . '] ' . $path;

		return $name;
	}
	
	public function guessTitleFromFile(File $file){
		if($file->mime_type === 'text/html'){
			$content = file_get_contents($file->path);
			
			if(strlen($content)>0){
				$str = trim(preg_replace('/\s+/', ' ', $content)); // supports line breaks inside <title>
				preg_match("/\<title\>(.*)\<\/title\>/i",$str,$title); // ignore case
				if(isset($title[1]) && !empty($title[1])){
//					\Log::info('Guessed File title', ['file' => $file, 'title' => $title[1]]);
					return trim($title[1]);
				}
			}
		}
		
		return $file->name;
	}


	// -- Helper functions to be moved out from here
	
	/**
	 * Makes a byte based size to a form understandable by humans
	 * 
	 * @param  float|integer  $bytes    The size in bytes to convert
	 * @param  integer $decimals (optional) the number of decimals to use
	 * @return string            The formatted size
	 */
	public static function human_filesize($bytes, $decimals = 2) {
	    $size = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');
	    $factor = floor((strlen($bytes) - 1) / 3);
	    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
	}

	/**
	 * Return the extension of the file
	 */
	public static function extension_from_file(\KlinkDMS\File $file) {
	    
		try{

		    return \KlinkDocumentUtils::getExtensionFromMimeType($file->mime_type);

		}catch(\Exception $ex){
			return '';
		}
	}

}
