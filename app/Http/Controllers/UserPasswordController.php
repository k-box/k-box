<?php

namespace KBox\Http\Controllers;

use Hash;
use KBox\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;

class UserPasswordController extends Controller
{

    /**
       * Create a new controller instance.
       *
       * @return void
       */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $pagetitle = trans('profile.password_section');

        return view('profile.password', compact('pagetitle'));
    }

    /**
     * used for Update user options (like the type of view of the lists)
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|min:8',
            'password_confirm' => 'required_with:password|same:password',
        ]);
        
        $user = $request->user();

        $user->password = Hash::make($request->get('password'));

        $user->save();

        event(new PasswordReset($user));

        return redirect()->route('profile.password.index')->with([
            'flash_message' => trans('profile.messages.password_changed')
        ]);
    }
}
