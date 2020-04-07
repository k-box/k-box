<?php

namespace KBox\Http\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class AllComposer
{

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

        if (! \Request::has('visibility')) {
            $view->with('current_visibility', $is_logged ? 'private' : 'public');
        }
        
        if ($is_logged) {
            $logged_in_user = \Auth::user();
            
            if (! Str::endsWith($logged_in_user->email, 'klink.local')) {
                $view->with('feedback_loggedin', $is_logged);
                $view->with('feedback_user_name', $logged_in_user->name);
                $view->with('feedback_user_mail', $logged_in_user->email);
            }
        }

        $route_name = \Route::currentRouteName();

        if (! is_null($route_name)  && $route_name != 'login') {
            $body_classes[] = $route_name;
        } elseif (! is_null($route_name)  && $route_name === 'login') {
            $body_classes[] = 'frontpage';
        }

        if (! is_null($route_name) && Str::startsWith($route_name, 'documents')) {
            $body_classes[] = 'dropzone-container';
        }

        if (is_null($route_name) && ! is_null(\Route::getCurrentRoute())) {
            $path = \Route::getCurrentRoute()->getPath();

            $exploded = array_slice(explode('/', $path), 0, 2);

            $body_classes[] =  implode(' ', $exploded);
        }

        $is_frontpage = $route_name === 'dashboard' || $route_name === 'frontpage' || $route_name === 'login';

        $view->with('is_frontpage', $is_frontpage);

        $already_added_classes = isset($view['body_classes']) ? $view['body_classes'] : null;

        if (! is_null($already_added_classes)) {
            $body_classes[] = $already_added_classes;
            
            $body_classes = array_unique($body_classes);
        }

        $view->with('body_classes', implode(' ', $body_classes));
    }
}
