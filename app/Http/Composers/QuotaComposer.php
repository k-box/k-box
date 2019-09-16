<?php

namespace KBox\Http\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Auth\Guard as Auth;
use KBox\Services\Quota;

class QuotaComposer
{
    private $service;
    private $auth;

    /**
     * Create a new profile composer.
     *
     * @param  Quota  $service The quota service
     * @param  Auth  $auth The authentication facade to retrieve the current user
     * @return void
     */
    public function __construct(Quota $service, Auth $auth)
    {
        $this->service = $service;
        $this->auth = $auth;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function widget(View $view)
    {
        if ($this->auth->check()) {
            $userquota = $this->service->user($this->auth->user());

            $view->with('unlimited', $userquota->unlimited);
            $view->with('percentage', $userquota->used_percentage);
            $view->with('used', human_filesize($userquota->used));
            $view->with('total', human_filesize($userquota->limit));
        }
    }
}
