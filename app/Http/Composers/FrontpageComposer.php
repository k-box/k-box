<?php

namespace KBox\Http\Composers;

use Illuminate\Contracts\View\View;

/**
    Layout composer for the Frontpage
*/
class FrontpageComposer
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
     * Bind data to the welcome view.
     *
     * @param  View  $view
     * @return void
     */
    public function welcome(View $view)
    {
        $body_classes = [];

        $is_logged = \Auth::check();

        $route_name = \Route::currentRouteName();
        

        if (! is_null($route_name)) {
            $body_classes[] = $route_name;
        }

        if (! is_null($route_name) && starts_with($route_name, 'documents')) {
            $body_classes[] = 'dropzone-container';
        }

        if (is_null($route_name) && ! is_null(\Route::getCurrentRoute())) {
            $path = \Route::getCurrentRoute()->getPath();

            $exploded = array_slice(explode('/', $path), 0, 2);

            $body_classes[] =  implode(' ', $exploded);
        }

        $is_frontpage = $route_name === 'dashboard' || $route_name === 'frontpage';

        $view->with('is_frontpage', $is_frontpage);

        $already_added_classes = isset($view['body_classes']) ? $view['body_classes'] : null;

        if (! is_null($already_added_classes)) {
            $body_classes[] = $already_added_classes;
            
            $body_classes = array_unique($body_classes);
        }

        $view->with('body_classes', implode(' ', $body_classes));
    }
}
