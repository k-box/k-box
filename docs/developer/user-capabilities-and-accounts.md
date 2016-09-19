# User capabilities and Accounts



Account type based on aggregation of capabilities




Capabilities name and function from the code point of view






**project editition**
```
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
        self::MANAGE_INSTITUTION_GROUPS,
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
        self::MANAGE_INSTITUTION_GROUPS,
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
        self::MANAGE_INSTITUTION_GROUPS,
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
    );
```
