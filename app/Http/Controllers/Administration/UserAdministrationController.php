<?php

namespace KBox\Http\Controllers\Administration;

use Exception;
use Log;
use KBox\Capability;
use KBox\Http\Controllers\Controller;
use KBox\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use KBox\User;
use KBox\Option;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerContract;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use KBox\Notifications\UserCreatedNotification;
use Illuminate\Support\Facades\DB;

class UserAdministrationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | User Management Controller
    |--------------------------------------------------------------------------
    |
    | This is responsible for creating/updating/disabling
    | user accounts.
    |
    */

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the list of registered users
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $data = [
            'users' => User::withTrashed()->get(),
            'pagetitle' => trans('administration.menu.accounts'),
            'current_user' => $request->user()->id
        ];

        if (! Option::isMailEnabled()) {
            $data['notices'] = [trans('notices.mail_testing_mode_msg', ['url' => route('administration.mail.index')])];
        }

        return view('administration.users', $data);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return Response
     */
    public function create()
    {
        $this->authorize('create', User::class);

        $user_types = [
        'partner' => Capability::$PARTNER,
        'project_admin' => Capability::$PROJECT_MANAGER,
        'admin' => Capability::$ADMIN,
        ];
      
        $type_resolutor = [];
      
        foreach ($user_types as $type_key => $type_caps) {
            $smandrupped = array_combine($type_caps, array_fill(0, count($type_caps), $type_key));
        
            foreach ($smandrupped as $elab_key => $elab_value) {
                if (isset($type_resolutor[$elab_key])) {
                    $type_resolutor[$elab_key][] = $elab_value;
                } else {
                    $type_resolutor[$elab_key]= [$elab_value];
                }
            }
        }

        // make the caps in order from the basic account type to the best account type
        $perms = array_flip(array_unique(array_merge(Capability::$PARTNER, Capability::$ADMIN)));

        $caps = Capability::all();

        foreach ($caps as $cap) {
            $perms[$cap->key] = $cap;
        }

        $viewBag = [
        'mode' => 'create',
        'user_types' => $user_types,
        'pagetitle' => trans('administration.accounts.create.title'),
        'capabilities' => array_values($perms),
        'type_resolutor' => $type_resolutor,
        'show_password_field' => ! Option::isMailEnabled() || (Option::isMailEnabled() && Option::isMailUsingLogDriver()),
        'disable_password_sending' => ! Option::isMailEnabled() || (Option::isMailEnabled() && Option::isMailUsingLogDriver()),
        ];
      
        return view('administration.users.create', $viewBag);
    }

    /**
     * Store a newly created user in storage.
     *
     * @return Response
     */
    public function store(UserRequest $request)
    {
        $this->authorize('create', User::class);

        $mail_configured = Option::isMailEnabled();

        $password = $request->input('password', null);

        if (empty($password)) {
            $password = User::generatePassword();
        }
        
        $send_password = $mail_configured && $request->input('send_password', false);
        
        $user = DB::transaction(function () use ($request, $password) {
            $user = User::create([
              'name' => $request->get('name'),
              'email' => trim($request->get('email')),
              'password' => Hash::make($password),
            ]);
    
            $user->addCapabilities($request->get('capabilities'));

            return $user;
        });

        try {
            if ($send_password) {
                $user->notify(new UserCreatedNotification($user, $password));
            }
        } catch (Exception $ex) {
            Log::error("User created notification cannot be sent", compact('ex'));

            return redirect()->route('administration.users.index')->with([
                'flash_message' => trans('administration.accounts.created_no_mail_msg')
            ]);
        }
      
        return redirect()->route('administration.users.index')->with([
            'flash_message' => ! $send_password ? trans('administration.accounts.created_msg') : trans('administration.accounts.created_password_sent_msg')
        ]);
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Guard $auth, $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('view', $user);

        return $this->edit($auth, $id);
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Guard $auth, $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);
      
        $user_types = [
            'partner' => Capability::$PARTNER,
            'project_admin' => Capability::$PROJECT_MANAGER,
            'admin' => Capability::$ADMIN,
        ];
      
        $type_resolutor = [];
      
        foreach ($user_types as $type_key => $type_caps) {
            $smandrupped = array_combine($type_caps, array_fill(0, count($type_caps), $type_key));
        
            foreach ($smandrupped as $elab_key => $elab_value) {
                if (isset($type_resolutor[$elab_key])) {
                    $type_resolutor[$elab_key][] = $elab_value;
                } else {
                    $type_resolutor[$elab_key]= [$elab_value];
                }
            }
        }
      
        // make the caps in order from the basic account type to the best account type
        $perms = array_flip(array_unique(array_merge(Capability::$PARTNER, Capability::$ADMIN)));
      
        $caps = Capability::all();
      
        foreach ($caps as $cap) {
            $perms[$cap->key] = $cap;
        }

        $viewBag = [
        'pagetitle' => trans('administration.accounts.edit_account_title', ['name' => $user->name]),
        'user' => $user,
        'user_types' => $user_types,
        'capabilities' => array_values($perms),
        'type_resolutor' => $type_resolutor,
        'edit_enabled' => $auth->user()->id != $user->id,
        'caps' => Arr::pluck($user->capabilities()->get()->toArray(), 'key')
        ];
      
        return view('administration.users.edit', $viewBag);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id the id of the user to update
     * @param $request The request
     * @return Response
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('update', $user);
      
        if ($request->filled('email')) {
            $change_mail = $request->get('email');
            $current_mail = $user->email;
          
            $already_exists = User::withTrashed()->fromEmail($change_mail)->first();
          
            if ($current_mail != $change_mail && is_null($already_exists)) {
                $user->email = $request->get('email');
            } elseif ($current_mail != $change_mail && ! is_null($already_exists)) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'The Email is already in use, please specify a different email address'
                ]);
            }
        }

        if ($user->name != $request->get('name')) {
            $user->name = $request->get('name');
        }
      
        $user->save();

        if ($request->has('capabilities')) {
            $current_submitted = $request->get('capabilities');
            $current_saved = Arr::pluck($user->capabilities()->get()->toArray(), 'key');
        
            DB::transaction(function () use ($current_saved, $current_submitted, $user) {
                $to_be_removed = array_diff($current_saved, $current_submitted);
    
                $to_be_added = array_diff($current_submitted, $current_saved);
            
                foreach ($to_be_added as $add) {
                    $user->addCapability($add);
                }
    
                foreach ($to_be_removed as $rem) {
                    $user->removeCapability($rem);
                }
    
                return true;
            });
        }

        return redirect()->route('administration.users.show', [$user->id])->with([
            'flash_message' => trans('administration.accounts.updated_msg')
        ]);
    }

    /**
     * In this case disable the specified user.
     *
     * @param  int  $id the user id to be disabled
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        $this->getValidationFactory()->make(
            ['user' => $user->getKey()],
            ['user' => 'required|not_in:'.$request->user()->getKey()],
            ['user.not_in' => __('You cannot disable yourself')]
        )->validate();

        $user->delete();

        return redirect()->route('administration.users.index')->with([
            'flash_message' => trans('administration.accounts.disabled_msg', ['name' => $user->name])
        ]);
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->where('id', $id)->first();
        
        $this->authorize('restore', $user);

        $user->restore();

        return redirect()->route('administration.users.index')->with([
              'flash_message' => trans('administration.accounts.enabled_msg', ['name' => $user->name])
          ]);
    }
    
    /**
     * Sends the reset password link from the Administration interface
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);

        try {
            $view = \Password::sendResetLink(['email' => $user->email, 'id' => $user->id]);
        
            if ($view == PasswordBrokerContract::INVALID_USER) {
                return redirect()->back()->withErrors([
                'error' => trans('administration.accounts.reset_not_sent_invalid_user', ['email' => $user->email])
                ]);
            } elseif ($view == PasswordBrokerContract::RESET_LINK_SENT) {
                return redirect()->back()->with([
                'flash_message' => trans('administration.accounts.reset_sent', ['name' => $user->name, 'email' => $user->email])
                ]);
            } else {
                return redirect()->back()->withErrors([
                'error' => trans('administration.accounts.reset_not_sent', ['email' => $user->email, 'error' => ''])
                ]);
            }
        } catch (\Exception $ex) {
            \Log::error('Password reset from admin interface error', ['error' => $ex]);
          
            return redirect()->back()->withErrors([
                'error' => trans('administration.accounts.reset_not_sent', ['email' => $user->email, 'error' => $ex->getMessage()])
            ]);
        }
    }
}
