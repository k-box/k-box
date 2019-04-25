<?php

namespace KBox\Http\Controllers;

use KBox\User;
use Illuminate\Http\Request;
use KBox\Events\EmailChanged;

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
            'email' => 'required|email|unique:users,email',
        ]);

        $user = $request->user();

        $before = $user->email;

        $user->email = e($request->get('email'));

        $user->save();

        event(new EmailChanged($user, $before, $user->email));

        $user->markEmailAsNotVerified();
        $user->sendEmailVerificationNotification();

        return redirect()->route('verification.notice')->with([
            'flash_message' => trans('profile.messages.mail_changed')
        ]);
    }
}
