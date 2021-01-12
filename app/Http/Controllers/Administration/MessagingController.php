<?php

namespace KBox\Http\Controllers\Administration;

use KBox\User;
use KBox\Option;
use Illuminate\Support\Str;
use KBox\Mail\UserDirectMessage;
use Illuminate\Support\Facades\Mail;
use KBox\Http\Controllers\Controller;
use KBox\Http\Requests\CreateMessageRequest;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use Illuminate\Support\Facades\Gate;

/**
 * Send Messages to the users
 */
class MessagingController extends Controller
{

  /*
  |--------------------------------------------------------------------------
  | Messaging Controller
  |--------------------------------------------------------------------------
  |
  | Handle messaging from the admins to the users.
  |
  */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new user.
     *
     * @return Response
     */
    public function create(AuthGuard $auth)
    {
        Gate::authorize('manage-kbox');
        
        $me = $auth->user();
    
        $available_users = User::whereNotIn('id', [$me->id])->get();
    
        $viewBag = [
         'pagetitle' => trans('messaging.create_pagetitle'),
         
         'available_users' => $available_users
          
        ];
      
        return view('administration.messaging.create', $viewBag);
    }

    /**
     * Store a newly created user in storage.
     *
     * @return Response
     */
    public function store(AuthGuard $auth, CreateMessageRequest $request)
    {
        Gate::authorize('manage-kbox');

        try {
            $me = $auth->user();
        
            $to = $request->get('to');
            if (! is_array($to)) {
                $to = [$to];
            }
        
            $to_users = User::whereIn('id', $request->get('to'))->get();
        
            $text = \Markdown::convertToHtml(e($request->get('text')));
        
            if ($to_users->isEmpty()) {
                return redirect()->back()->withInput()->withErrors([
                    'error' => trans('messaging.message_error', ['error' => trans('messaging.error_empty_users')])
                ]);
            }
        
            if ($to_users->count() !== count($to)) {
                return redirect()->back()->withInput()->withErrors([
                    'error' => trans('messaging.error_users_not_found', ['users' => implode(',', array_diff($to_users->all(), $to))])
                ]);
            }
        
            $from_mail = Option::mailFrom();
            $from_name = Option::mailFromName();
        
            if (! Str::endsWith($me->email, 'klink.local')) {
                $from_name = $me->name;
            }
        
            foreach ($to_users as $user) {
                Mail::queue(new UserDirectMessage($me, $user, $text));
            }
    
            return redirect()->route('administration.users.index')->with([
                'flash_message' => trans('messaging.message_sent')
            ]);
        } catch (\Exception $ex) {
            return redirect()->back()->withInput()->withErrors([
                'error' => trans('messaging.message_error', ['error' => $ex->getMessage()])
            ]);
        }
    }
}
