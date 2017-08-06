<?php

namespace KlinkDMS\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use KlinkDMS\Capability;

class ViewComposerServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('global', '\KlinkDMS\Http\Composers\AllComposer');
        view()->composer('search', 'KlinkDMS\Http\Composers\HeadersComposer');
        view()->composer('profile.user', 'KlinkDMS\Http\Composers\HeadersComposer');
        
        $this->registerHeadersComposer();
        
        $this->registerFrontpageComposer();

        $this->registerWidgetComposers();

        $this->registerGroupComposers();

        $this->registerDocumentComposers();

        $this->registerNoticesComposer();

        $this->registerAvatarComposer();

        $this->registerListSwitcher();
        
        $this->registerMenuComposer();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }

    public function registerHeadersComposer()
    {
        view()->composer('headers.header', 'KlinkDMS\Http\Composers\HeadersComposer');
        // view()->composer('headers.external', 'KlinkDMS\Http\Composers\HeadersComposer');
        view()->composer('login-layout', 'KlinkDMS\Http\Composers\HeadersComposer');
        view()->composer('share.create', 'KlinkDMS\Http\Composers\HeadersComposer');
    }
    
    public function registerFrontpageComposer()
    {
    }

    private function registerWidgetComposers()
    {
        view()->composer('widgets.starred-documents', 'KlinkDMS\Http\Composers\WidgetsComposer@widgetStarred');

        view()->composer('widgets.storage', 'KlinkDMS\Http\Composers\WidgetsComposer@widgetStorage');

        view()->composer('widgets.recent-searches', 'KlinkDMS\Http\Composers\WidgetsComposer@widgetRecentSearches');
        
        view()->composer('widgets.users-sessions', 'KlinkDMS\Http\Composers\WidgetsComposer@widgetUserSessions');
    }

    private function registerGroupComposers()
    {
        view()->composer('groups.tree', 'KlinkDMS\Http\Composers\GroupsComposer@tree');

        view()->composer('groups.group', 'KlinkDMS\Http\Composers\GroupsComposer@group');

        view()->composer('groups.groupform', 'KlinkDMS\Http\Composers\GroupsComposer@groupForm');
    }

    private function registerDocumentComposers()
    {
        view()->composer('documents.document-layout', 'KlinkDMS\Http\Composers\DocumentsComposer@layout');
        view()->composer('documents.projects.layout', 'KlinkDMS\Http\Composers\DocumentsComposer@layout');
        
        view()->composer('documents.documents', 'KlinkDMS\Http\Composers\DocumentsComposer@layout');
        view()->composer('documents.recent', 'KlinkDMS\Http\Composers\DocumentsComposer@layout');
        view()->composer('documents.trash', 'KlinkDMS\Http\Composers\DocumentsComposer@layout');
        view()->composer('documents.starred', 'KlinkDMS\Http\Composers\DocumentsComposer@layout');
        view()->composer('documents.sharedwithme', 'KlinkDMS\Http\Composers\DocumentsComposer@shared');
        view()->composer('documents.menu', 'KlinkDMS\Http\Composers\DocumentsComposer@menu');
        
        view()->composer('documents.trash', 'KlinkDMS\Http\Composers\DocumentsComposer@layout');

        view()->composer('documents.descriptor', 'KlinkDMS\Http\Composers\DocumentsComposer@descriptor');

        view()->composer('panels.document', 'KlinkDMS\Http\Composers\DocumentsComposer@descriptorPanel');

        view()->composer('documents.edit', 'KlinkDMS\Http\Composers\DocumentsComposer@descriptorPanel');

        view()->composer('documents.partials.versioninfo', 'KlinkDMS\Http\Composers\DocumentsComposer@versionInfo');
        
        view()->composer('documents.preview', 'KlinkDMS\Http\Composers\DocumentsComposer@descriptorPanel');
        
        view()->composer('documents.facets', 'KlinkDMS\Http\Composers\DocumentsComposer@facets');
        view()->composer('documents.group-facets', 'KlinkDMS\Http\Composers\DocumentsComposer@groupFacets');
    }

    private function registerNoticesComposer()
    {
    }

    private function registerAvatarComposer()
    {
        view()->composer('avatar.picture', function ($view) {
            $user = isset($view['user_name']) ? $view['user_name'] : \Auth::user()->name;

            // $user_name = studly_case($user);

            // $view->with('user_initial', $user_name[0]);
            $view->with('avatar_color', '#34495e');
        });
    }

    private function registerListSwitcher()
    {
        view()->composer('actions.list-switcher', function ($view) {
            if (\Auth::check()) {
                $user = \Auth::user();

                $view->with('list_style_current', $user->optionListStyle());
            } else {
                $view->with('list_style_current', 'tiles');
            }
        });
    }
    
    
    private function registerMenuComposer()
    {
        view()->composer('menu', function ($view) {
            if (\Auth::check()) {
                $user = \Auth::user();
                
                $show_admin_link = $user->isDMSManager();
                $show_doc_link = $user->isContentManager() || $user->can_capability(Capability::UPLOAD_DOCUMENTS) || $user->can_capability(Capability::EDIT_DOCUMENT);

                $people_group = $user->can_capability([Capability::MANAGE_PEOPLE_GROUPS, Capability::MANAGE_PERSONAL_PEOPLE_GROUPS]);

                $show_shared_link = ! $show_doc_link && $user->can_capability(Capability::RECEIVE_AND_SEE_SHARE) && $user->can_capability(Capability::MAKE_SEARCH);
                
                $show_search_link = ! $show_doc_link && $user->can_capability(Capability::RECEIVE_AND_SEE_SHARE) && $user->can_capability(Capability::MAKE_SEARCH);

                $view->with('show_admin_link', $show_admin_link);
                
                $view->with('show_doc_link', $show_doc_link);
                $view->with('show_projects_link', $user->isProjectManager());
                
                $view->with('show_groups_link', $people_group);
                
                $view->with('show_shared_link', $show_shared_link);
                $view->with('show_search_link', $show_search_link);
            } else {
                $view->with('show_admin_link', false);
                $view->with('show_doc_link', false);
                $view->with('show_groups_link', false);
                $view->with('show_shared_link', false);
                $view->with('show_search_link', false);
                $view->with('show_projects_link', false);
            }
        });
    }
}
