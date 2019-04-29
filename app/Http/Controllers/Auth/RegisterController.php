<?php

namespace KBox\Http\Controllers\Auth;

use KBox\User;
use KBox\Capability;
use Illuminate\Support\Str;
use KBox\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
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

        return $user;
    }
}
