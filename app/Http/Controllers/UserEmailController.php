<?php

namespace KBox\Http\Controllers;

use KBox\User;
use Illuminate\Http\Request;

/**
 * Change user email
 */
class UserEmailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $pagetitle = trans('profile.email_section');
        $user = $request->user();

        return view('profile.email', compact('pagetitle', 'user'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'email' => 'sometimes|required|email|unique:users,email',
        ]);

        $user = $request->user();

        $user->email = e($request->get('email'));

        $user->save();

        return redirect()->route('profile.email.index')->with([
            'flash_message' => trans('profile.messages.mail_changed')
        ]);
    }
}
