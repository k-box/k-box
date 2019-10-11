<?php

namespace KBox\Http\Composers;

use KBox\Group;
use KBox\Capability;
use Illuminate\Contracts\View\View;
use KBox\Documents\Services\DocumentsService;

class GroupsComposer
{

    /**
     * @var \KBox\Documents\Services\DocumentsService
     */
    private $documents = null;

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct(DocumentsService $documentsService)
    {
        $this->documents = $documentsService;
    }

    /**
     * Tree template for Group display
     *
     * @param  View  $view
     * @return void
     */
    public function tree(View $view)
    {
        if (\Auth::check()) {
            $auth_user = \Auth::user();
            
            $view->with('current_user', $auth_user->id);
            
            $can_personal = $auth_user->can_capability(Capability::MANAGE_OWN_GROUPS);
            
            $can_see_private = $auth_user->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS);
            
            $can_edit_private = $auth_user->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS);
            
            $view->with('user_can_edit_personal_groups', $can_personal);
            $view->with('user_can_see_private_groups', $can_see_private);
            $view->with('user_can_edit_private_groups', $can_edit_private);
            
            $collections = $this->documents->getCollectionsAccessibleByUser($auth_user);
            
            $view->with('personal_groups', $collections->personal);
            $view->with('private_groups', $collections->projects);
            $view->with('shared_groups', $collections->shared);
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
        if (\Auth::check()) {
            $auth_user = \Auth::user();

            $group = isset($view['item']) ? $view['item'] : $view['group'];

            $view->with('badge_shared', false);
        }
    }

    public function groupForm(View $view)
    {
        if (\Auth::check() /*&& isset($view['group']) */) {
            $auth_user = \Auth::user();

            // $group = $view['group'];

            $view->with('user_can_edit_private_groups', $auth_user->can_capability(Capability::MANAGE_OWN_GROUPS));
            $view->with('user_can_edit_public_groups', $auth_user->can_capability(Capability::MANAGE_PROJECT_COLLECTIONS));
        }
    }
}
