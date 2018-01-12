<?php

namespace KBox\Http\Composers;

use KBox\Option;
use Illuminate\Contracts\View\View;

class CopyrightComposer
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
        $view->with('available_licenses', Option::copyright_available_licenses());
    }
}
