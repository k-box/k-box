<?php

namespace KBox\Http\Controllers\Auth;

use Exception;
use KBox\User;
use KBox\Capability;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use KBox\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use KBox\Auth\Registration;
use KBox\Invite;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/email/verify';

    protected $registerView = 'auth.register';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');

        abort_if(! Registration::isEnabled(), 403, __('User registration is not active on this instance'));
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm(Request $request)
    {
        $invite = $this->extractInviteFromRequest($request);

        if ($invite === false) {
            return view($this->registerView);
        }

        if (is_null($invite)) {
            return view($this->registerView, [
                'invite_error' => trans('invite.invalid')
            ]);
        }

        return view($this->registerView, [
            'email' => $invite->email,
            'invite' => $invite->token
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $val = Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            
            // verify invite
            'invite' => [
                Registration::requiresInvite() ? 'required' : 'sometimes',
                'nullable',
                'string',
                'max:100',
                'exists:invites,token'],

            // We choose to not require a user name, but if specified we accept it
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        return $val;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \KBox\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'] ?? Str::before($data['email'], '@'),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->addCapabilities(Capability::$PARTNER);

        if (isset($data['invite']) && $data['invite']) {
            $invite = Invite::valid()->hasToken($data['invite'])->first();

            if ($invite) {
                $invite->accept($user);

                if ($invite->email === $user->email) {
                    $user->markEmailAsVerified();
                }
            }
        }

        return $user;
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        if (! $user->hasVerifiedEmail()) {
            return null;
        }
        
        return redirect('/');
    }

    private function extractInviteFromRequest(Request $request)
    {
        if (! $request->has('signature')) {
            return false;
        }

        if (! $request->hasValidSignature()) {
            return null;
        }

        try {
            $invite = Invite::whereUuid(e($request->input('i')))->first();
        } catch (Exception $ex) {
            return null;
        }

        if (is_null($invite) || ($invite && ($invite->isExpired() || $invite->wasAccepted()))) {
            return null;
        }

        return $invite;
    }
}
