<?php

namespace KBox\Http\Controllers\Identities\Auth;

use Illuminate\Http\Request;
use KBox\User;
use KBox\Http\Controllers\Controller;
use Oneofftech\Identities\Auth\ConnectUserIdentity;

class ConnectController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Connect Identity Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the connection (or update) of an
    | identity for an already authenticated user.
    | The controller uses a trait to conveniently provide its
    | functionality to your applications.
    |
    */

    use ConnectUserIdentity;

    protected $attributes = ['b'];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function connected($user, $identity, array $attributes, Request $request)
    {
        $b = $attributes['b'] ?? null;

        if ($b === 'profile') {
            return redirect()->route('profile.identities.index')->with('flash_message', __(':Provider connected', ['provider' => $identity->provider]));
        }

        return redirect($this->getPreviousUrl());
    }
}
