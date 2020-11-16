<?php

namespace KBox\Http\Controllers\Identities\Auth;

use KBox\User;
use Illuminate\Support\Str;
use KBox\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use KBox\Auth\Registration;
use Oneofftech\Identities\Auth\RegistersUsersWithIdentity;
use KBox\Capability;
use KBox\Invite;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register via Identity Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users via identities
    | provided by third party authentication services. The controller
    | uses a trait to conveniently provide its functionality.
    |
    */

    use RegistersUsersWithIdentity;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    protected $attributes = ['invite'];

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
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['sometimes', 'required', 'string', 'min:8', 'confirmed'],
            
            // verify invite
            'invite' => [
                Registration::requiresInvite() ? 'required' : 'sometimes',
                'nullable',
                'string',
                'max:100',
                'exists:invites,token'
            ],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Auth\Authenticatable|\KBox\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'] ?? Str::before($data['email'], '@'),
            'email' => $data['email'],
            'password' => Hash::make($data['password'] ?? Str::random(20)),
        ]);

        $user->addCapabilities(Capability::$PARTNER);

        // we just assume that the other service
        // has verified the users' email
        $user->markEmailAsVerified();

        if (isset($data['invite']) && $data['invite']) {
            $invite = Invite::valid()->hasToken($data['invite'])->first();

            if ($invite) {
                $invite->accept($user);
            }
        }

        return $user;
    }
}
