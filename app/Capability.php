<?php namespace KlinkDMS;

use Illuminate\Database\Eloquent\Model;

class Capability extends Model {
    /*
    id: increments
    key: string
    description: string
    */



    ////////////////////////////////////////////////////////////////////////
    // The magic collection of contants for the names of the capabilities //
    ////////////////////////////////////////////////////////////////////////

    /**
     * Enable the access to the administration dashboard
     */
    const MANAGE_DMS = 'manage_dms';

    /**
     * Add the ability to create, edit and remove Users from the DMS
     */
    const MANAGE_USERS = 'manage_dms_users';

    /**
     * Access and manage the DMS logs
     */
    const MANAGE_LOG = 'manage_dms_log';

    /**
     * Access and Manage DMS backups
     */
    const MANAGE_BACKUP = 'manage_dms_backup';

    
    /**
     * The user can edit a document
     */
    const EDIT_DOCUMENT = 'edit_document';
    
    /**
     * The user can delete a document (put in trash)
     */
    const DELETE_DOCUMENT = 'delete_document';
    
    /**
     * The use can change a document visibility from private to public
     */
    const CHANGE_DOCUMENT_VISIBILITY = 'change_document_visibility';

    /**
     * Can perform import
     */
    const IMPORT_DOCUMENTS = 'import_documents';

    /**
     * Can perform upload of new documents (add)
     */
    const UPLOAD_DOCUMENTS = 'upload_documents';

    /**
     * Enable the user to perform private searches
     */
    const MAKE_SEARCH = 'make_search';

    /**
     * The user can create, edit or remove document groups
     */
    const MANAGE_OWN_GROUPS = 'manage_own_groups';

    /**
     * The user can create, edit or remove document groups that are
     * visible to all the users of the institution
     */
    const MANAGE_INSTITUTION_GROUPS = 'manage_institution_groups';
    
    /**
     * The user can create/edit/remove collections under a project
     */
    const MANAGE_PROJECT_COLLECTIONS = 'manage_project_collections';

    /**
     * User may share private documents with a single or a personal group of users
     */
    const SHARE_WITH_PERSONAL = 'manage_share_personal';
    
    /**
     * User may share private documents with a single or a people group of users
     */
    const SHARE_WITH_PRIVATE = 'manage_share_private';
    
    /**
     * The user can see a shared documents (be a target of a share)
     */
    const RECEIVE_AND_SEE_SHARE = 'receive_share';
    
    /**
     * The user can permanently delete the trash content
     */
    const CLEAN_TRASH = 'clean_trash';
    
    /**
     * Create/edit/remove groups of people at the institution level
     */
    const MANAGE_PEOPLE_GROUPS = 'manage_people';
    
    /**
     * Create/edit/remove groups of people at the user's personal level
     */
    const MANAGE_PERSONAL_PEOPLE_GROUPS = 'manage_personal_people';


    ////////////////////////////////////
    // Predefined set of capabilities //
    ////////////////////////////////////
    
    static $ADMIN = array( 
        self::MAKE_SEARCH, 
        self::UPLOAD_DOCUMENTS, 
        self::IMPORT_DOCUMENTS, 
        self::EDIT_DOCUMENT,
        self::CHANGE_DOCUMENT_VISIBILITY,
        self::DELETE_DOCUMENT,
        self::MANAGE_OWN_GROUPS,
        self::MANAGE_PROJECT_COLLECTIONS,
        self::MANAGE_DMS,
        self::MANAGE_USERS,
        self::MANAGE_LOG,
        self::MANAGE_BACKUP,
        self::RECEIVE_AND_SEE_SHARE,
        self::CLEAN_TRASH,
        self::MANAGE_PEOPLE_GROUPS,
        self::MANAGE_PERSONAL_PEOPLE_GROUPS,
        self::SHARE_WITH_PERSONAL,
        self::SHARE_WITH_PRIVATE );

    static $DMS_MASTER = array( 
        self::MANAGE_DMS,
        self::MANAGE_USERS,
        self::MANAGE_LOG,
        self::MANAGE_BACKUP );

    static $CONTENT_MANAGER = array( 
        self::MAKE_SEARCH, 
        self::UPLOAD_DOCUMENTS, 
        self::IMPORT_DOCUMENTS,
        self::MANAGE_OWN_GROUPS,
        self::DELETE_DOCUMENT,
        self::EDIT_DOCUMENT,
        self::RECEIVE_AND_SEE_SHARE,
        self::MANAGE_PERSONAL_PEOPLE_GROUPS,
        self::SHARE_WITH_PERSONAL,
        self::SHARE_WITH_PRIVATE );
        
    // is the project manager for the Project edition
    static $QUALITY_CONTENT_MANAGER = array( 
        self::MAKE_SEARCH, 
        self::UPLOAD_DOCUMENTS, 
        self::IMPORT_DOCUMENTS,
        self::MANAGE_OWN_GROUPS,
        self::MANAGE_PROJECT_COLLECTIONS,
        self::DELETE_DOCUMENT,
        self::EDIT_DOCUMENT, 
        self::CHANGE_DOCUMENT_VISIBILITY,
        self::MANAGE_PEOPLE_GROUPS,
        self::MANAGE_PERSONAL_PEOPLE_GROUPS,
        self::RECEIVE_AND_SEE_SHARE,
        self::CLEAN_TRASH,
        self::SHARE_WITH_PERSONAL,
        self::SHARE_WITH_PRIVATE );
        
    static $PROJECT_MANAGER = array( 
        self::MAKE_SEARCH, 
        self::UPLOAD_DOCUMENTS, 
        self::IMPORT_DOCUMENTS,
        self::MANAGE_OWN_GROUPS,
        self::MANAGE_PROJECT_COLLECTIONS,
        self::DELETE_DOCUMENT,
        self::EDIT_DOCUMENT, 
        self::CHANGE_DOCUMENT_VISIBILITY,
        self::MANAGE_PEOPLE_GROUPS,
        self::MANAGE_PERSONAL_PEOPLE_GROUPS,
        self::RECEIVE_AND_SEE_SHARE,
        self::CLEAN_TRASH,
        self::SHARE_WITH_PERSONAL,
        self::SHARE_WITH_PRIVATE );

    static $UPLOADER = array( 
        self::UPLOAD_DOCUMENTS,
        self::MANAGE_OWN_GROUPS,
        self::EDIT_DOCUMENT,
        self::DELETE_DOCUMENT,
        self::RECEIVE_AND_SEE_SHARE,
        self::SHARE_WITH_PERSONAL );

    static $PARTNER = array( 
        self::MAKE_SEARCH,
        self::RECEIVE_AND_SEE_SHARE,
        self::UPLOAD_DOCUMENTS,
        self::DELETE_DOCUMENT,
        self::MANAGE_OWN_GROUPS,
        self::SHARE_WITH_PERSONAL,
        self::MANAGE_PROJECT_COLLECTIONS,
        self::EDIT_DOCUMENT, );

    static $GUEST = array( 
        self::RECEIVE_AND_SEE_SHARE );
        
        
    static $OLD_NEW_MAPPING = array(
          'manage_institution_documents' => array(
              self::CHANGE_DOCUMENT_VISIBILITY, 
              self::EDIT_DOCUMENT, 
              self::DELETE_DOCUMENT),
          'manage_institution_documents_visibility' => array(
              self::CHANGE_DOCUMENT_VISIBILITY),
          'manage_own_documents' => array(
              self::EDIT_DOCUMENT, 
              self::DELETE_DOCUMENT),
          'manage_own_documents_visibility' => array(
              self::CHANGE_DOCUMENT_VISIBILITY),
          'manage_institution_groups' => array(
              self::MANAGE_PROJECT_COLLECTIONS),
    );


    ////////////////////////////////////
    // The rest of the Eloquent Model //
    ////////////////////////////////////

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'capabilities';

    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['key', 'description'];


    /**
     * Retrieve a capability from the associated label key
     * @param  [type] $query [description]
     * @param  [type] $key   [description]
     * @return [type]        [description]
     */
    public function scopeFromKey($query, $key)
    {
        return $query->whereKey($key);
    }

    public function scopeFromKeys($query, array $keys)
    {
        return $query->whereIn('key', $keys);
        // Product::whereIn('parent_id', $array)->get();
    }

    private static function getConstants(){
        $oClass = new \ReflectionClass('KlinkDMS\Capability');

        return array_filter($oClass->getConstants(), function($el){
            
            return $el !== self::CREATED_AT && $el !== self::UPDATED_AT; 
        });
    }

    /**
     * Get all known capabilities
     */
    public static function known(){
        
        $known_constants = self::getConstants();
        
        return Capability::fromKeys(array_values($known_constants))->get();
        
    }
    
    
    /**
     * check if the Capabilities table is in sync with new 
     * capabilities and, if not, add them to the database (old unused capabilities are not removed)
     */
    public static function syncCapabilities(){
        
        \Log::info('Sync Capabilities called');
    
        $current_caps = self::all(array('key'))->fetch('key')->toArray();
    
        $constants = array_values(self::getConstants());
        
        
    
        $difference = array_diff($current_caps, $constants);
        
        $new = array_diff($constants, $current_caps);
    
        $needed = !empty($new); // check if upgrade is needed
        
        if(!$needed){
            \Log::info('Capability upgrade not needed, constants and capabilities table are in sync');
            return false;
        }
        
            
        $executed = \DB::transaction(function() use($current_caps, $constants, $difference, $new) {
            
            // if yes create the new capabilities
            
            $new_caps_ids = array();
            
            foreach($new as $to_add){
               $n_cap = Capability::create(array('key' => $to_add)); 
               
               \Log::info('Added new capability', array('cap' => $n_cap));
               
               $new_caps_ids[$n_cap->key] = $n_cap->id;
            }
            

            
            // get the current users and their capabilities
            
            // remove the old capabilities and add the new ones according to the mapping
            
            $mappings = self::$OLD_NEW_MAPPING;
            
            $old_keys = array_keys($mappings);
            
            
            $users = User::all();
            
            foreach($users as $user){
                
                foreach($old_keys as $o_key){
                
                    if($user->can($o_key)){
                        
                        $new_caps_to_add = Capability::fromKeys($mappings[$o_key])->get();
                        
                        \Log::info('Upgrading capabilities to user ' . $user->id, array('old' => $o_key, 'new' => $new_caps_to_add));
                        
                        foreach($new_caps_to_add as $new_cap){
                            $user->addCapability($new_cap);
                        }

                        $user->removeCapability($o_key);
                    }
                }
            }
            
            $removed = array_values(Capability::whereIn('key', $old_keys)->get(array('id'))->fetch('id')->toArray());
            
            Capability::destroy($removed);
            
            
            return true;
        });
    
        return $executed;
    }
    

}
