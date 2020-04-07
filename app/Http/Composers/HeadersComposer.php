<?php

namespace KBox\Http\Composers;

use KBox\HomeRoute;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;

class HeadersComposer
{
    private static $search_target_for_routes = [
        'documents.show' => 'documents.index',
        'documents.edit' => 'documents.index',
        'frontpage' => 'search'
    ];

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $body_classes = [];

        $is_logged = \Auth::check();

        $view->with('is_user_logged', $is_logged);

        if ($is_logged) {
            $logged_in_user = \Auth::user();
            $view->with('current_user', $logged_in_user->id);
            $view->with('current_user_home_route', HomeRoute::get($logged_in_user));
            $view->with('current_user_name', $logged_in_user->name);
            $view->with('current_user_avatar', $logged_in_user->avatar);

            $view->with('list_style_current', $logged_in_user->optionListStyle());
        } else {
            $view->with('list_style_current', 'tiles');
        }

        $route_name = \Route::currentRouteName();

        $is_klink_public_enabled = config('dms.are_guest_public_search_enabled') && network_enabled();

        $show_search = (! $is_logged && $is_klink_public_enabled && ! Str::startsWith($route_name, 'password') && ! Str::contains($route_name, 'help') && ! Str::startsWith($route_name, 'terms') && ! Str::contains($route_name, 'contact')) ||
                        ($is_logged && ! is_null($route_name) &&
                       (! Str::startsWith($route_name, 'admin') ||  Str::startsWith($route_name, 'admin') && Str::contains($route_name, 'storage.files'))  &&
                       ! Str::contains($route_name, 'contact') &&
                       ! Str::contains($route_name, 'help') && ! Str::startsWith($route_name, 'terms') && ! Str::contains($route_name, 'trash') &&
                       ! Str::startsWith($route_name, 'projects')  &&
                       ! Str::contains($route_name, 'profile.') &&
                       ! Str::startsWith($route_name, 'consent') &&
                       ! Str::startsWith($route_name, 'register') &&
                       ! Str::startsWith($route_name, 'verification') &&
                       ! Str::startsWith($route_name, 'documents.edit') &&
                       ! Str::startsWith($route_name, 'plugins') &&
                       ! Str::startsWith($route_name, 'uploads') &&
                       ! Str::startsWith($route_name, 'privacy') &&
                       ! Str::startsWith($route_name, 'password') && ! Str::startsWith($route_name, 'microsite'));

        $view->with('show_search', $show_search);

        $view->with('search_target', $show_search && array_key_exists($route_name, self::$search_target_for_routes) ? self::$search_target_for_routes[$route_name] : $route_name);
        
        $view->with('search_target_parameters', $show_search && ! array_key_exists($route_name, self::$search_target_for_routes) ? \Route::current()->parameters() : false);
        
        $is_frontpage = $route_name === 'dashboard' || $route_name === 'frontpage';

        $view->with('is_frontpage', $is_frontpage);
    }
}
