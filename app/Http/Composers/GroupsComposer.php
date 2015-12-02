<?php namespace KlinkDMS\Http\Composers;


use KlinkDMS\Group;
use KlinkDMS\Capability;
use Illuminate\Contracts\View\View;

use Illuminate\Contracts\Auth\Guard as AuthGuard;

class GroupsComposer {

    /**
     * ...
     *
     * @var 
     */
    protected $adapter;

    /**
     * [$documents description]
     * @var \Klink\DmsDocuments\DocumentsService
     */
    private $documents = NULL;

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct()
    {
        //\Klink\DmsAdapter\KlinkAdapter $adapter, \Klink\DmsDocuments\DocumentsService $documentsService
//        $this->adapter = $adapter;
//
//        $this->documents = $documentsService;
    }

    /**
     * Tree template for Group display
     *
     * @param  View  $view
     * @return void
     */
    public function tree(View $view)
    {
        if(\Auth::check()){
            
            $auth_user = \Auth::user();
            
            $view->with('current_user', $auth_user->id);
            
            $can_personal = $auth_user->can(Capability::MANAGE_OWN_GROUPS);
            
            $can_see_private = $auth_user->can(Capability::MANAGE_INSTITUTION_GROUPS);
            
            $can_edit_private = $auth_user->can(Capability::MANAGE_INSTITUTION_GROUPS);
            
            $view->with('user_can_edit_personal_groups', $can_personal);
            $view->with('user_can_see_private_groups', $can_see_private);
            $view->with('user_can_edit_private_groups', $can_edit_private);

            if($can_see_private){
                $private_groups = \Cache::remember('dms_institution_collections', 60, function(){
                    return Group::getTreeWhere('is_private', '=', false);
                });
                
                $view->with('private_groups', $private_groups);
            }

            if($can_personal){
                $personal_groups = \Cache::remember('dms_personal_collections'.$auth_user->id, 60, function() use($auth_user) {
                    return Group::getPersonalTree($auth_user->id);
                });
    
                $view->with('personal_groups', $personal_groups);
            }

        }
    }

    /**
     * Tree template for Group display
     *
     * @param  View  $view
     * @return void
     */
    public function group(View $view)
    {
        if(\Auth::check()){

            $auth_user = \Auth::user();

            $group = isset($view['item']) ? $view['item'] : $view['group'];

            $view->with('badge_shared', false);

            //check if a group is shared
            //
            //check docs count for subgrid the thumbnail

            // if($auth_user->isDMSAdmin() || $auth_user->isContentManager()){

            //     $storage = $this->documents->getStorageStatus();

            //     $view->with('storage_status', $storage);

            // }
        }
    }


    public function groupForm(View $view)
    {
        if(\Auth::check() /*&& isset($view['group']) */){

            $auth_user = \Auth::user();

            // $group = $view['group'];

            
            $view->with('user_can_edit_private_groups', $auth_user->can(Capability::MANAGE_OWN_GROUPS));
            $view->with('user_can_edit_public_groups', $auth_user->can(Capability::MANAGE_INSTITUTION_GROUPS));
            
        }
    }


}
