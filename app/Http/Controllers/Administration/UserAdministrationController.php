<?php namespace KlinkDMS\Http\Controllers\Administration;

use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Institution;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\Http\Requests\UserRequest;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use KlinkDMS\User;
use KlinkDMS\Option;
use Illuminate\Contracts\Auth\Guard;
use Klink\DmsAdapter\KlinkAdapter;
use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerContract;

/**
 * User Resource Controller
 */
class UserAdministrationController extends Controller {

  /*
  |--------------------------------------------------------------------------
  | User Management Page Controller
  |--------------------------------------------------------------------------
  |
  | This controller respond to ations for the "users management page".
  |
  */

  private $adapter = null;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(KlinkAdapter $adapter) {

    $this->middleware('auth');

    $this->middleware('capabilities');

    $this->adapter = $adapter;
  }

  /**
   * Show the list of registered users
   *
   * @return Response
   */
  public function index(Guard $auth) {

    $users = User::withTrashed()->get();
    $data = ['users' => $users, 'pagetitle' => trans('administration.menu.accounts')];

    if(!Option::isMailEnabled())
    {
        $data['notices'] = [trans('notices.mail_testing_mode_msg', ['url' => route('administration.mail.index')])];
    }

    if($auth->check()){
      $data['current_user'] = $auth->user()->id;
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
    
    $user_types = array(
        'guest' => Capability::$GUEST,
        'partner' => Capability::$PARTNER,
        'project_admin' => Capability::$QUALITY_CONTENT_MANAGER,
        'klinker' => Capability::$QUALITY_CONTENT_MANAGER,
        'admin' => Capability::$ADMIN,
      );
      
      
      $type_resolutor = array();
      
      foreach($user_types as $type_key => $type_caps){
        
        $smandrupped = array_combine($type_caps, array_fill(0, count($type_caps), $type_key));
        
        foreach($smandrupped as $elab_key => $elab_value){
          
          if(isset($type_resolutor[$elab_key])){
            $type_resolutor[$elab_key][] = $elab_value;
          }
          else {
            $type_resolutor[$elab_key]= array($elab_value);
          }
        }
        
        
        
        
      }
      
      // make the caps in order from the basic account type to the best account type
      $perms = array_flip(array_unique(array_merge(Capability::$GUEST, Capability::$PARTNER, Capability::$CONTENT_MANAGER, Capability::$QUALITY_CONTENT_MANAGER, Capability::$ADMIN)));
      
      $caps = Capability::all();
      
      foreach($caps as $cap){
        $perms[$cap->key] = $cap;
      }
    
    
      $institutions = Institution::all();
    

      $viewBag = [
        'mode' => 'create',
        'institutions' => $institutions,
        'user_types' => $user_types,
        'pagetitle' => trans('administration.accounts.create.title'),
        'capabilities' => array_values($perms),
        'type_resolutor' => $type_resolutor,
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

      if(!Option::isMailEnabled())
      {
          return redirect()->back()->withInput()->withErrors([
               'email' => trans('notices.mail_not_configured', ['url' => route('administration.mail.index')])
          ]);
      }

      $password = User::generatePassword();

      $user = \DB::transaction(function() use($request, $password){
      
          $user = User::create([
              'name' => $request->get('name'),
              'email' => trim($request->get('email')),
              'password' => Hash::make($password),
              'institution_id' => $request->get('institution', null)
          ]);
    
          $user->addCapabilities($request->get('capabilities'));
      
      
          return $user;
      });

      \Mail::queue('emails.welcome-html',
        array('user' => $user, 'password' => $password),
        function ($message) use ($user) {
          $message->to($user->email, $user->name)->subject(trans('administration.accounts.mail_subject'));
        });

      return redirect()->route('administration.users.index')->with([
            'flash_message' => trans('administration.accounts.created_msg')
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
      // $user = User::findOrFail($id);

      // $viewBag = [
      //     'user' => $user,
      //     'capabilities' => Capability::all(),
      //     'caps' => array_pluck($user->capabilities()->get()->toArray(), 'key')
      //   ];
      



      return $this->edit($auth, $id); //view('administration.users.edit', $viewBag);
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
      
      $user_types = array(
        'guest' => Capability::$GUEST,
        'partner' => Capability::$PARTNER,
        // 'content_manager' => Capability::$CONTENT_MANAGER,
        // 'quality_content_manager' => Capability::$QUALITY_CONTENT_MANAGER,
        
        'project_admin' => Capability::$PROJECT_MANAGER,
        'klinker' => Capability::$PROJECT_MANAGER,
        'admin' => Capability::$ADMIN,
      );
      
      
      $type_resolutor = array();
      
      foreach($user_types as $type_key => $type_caps){
        
        $smandrupped = array_combine($type_caps, array_fill(0, count($type_caps), $type_key));
        
        foreach($smandrupped as $elab_key => $elab_value){
          
          if(isset($type_resolutor[$elab_key])){
            $type_resolutor[$elab_key][] = $elab_value;
          }
          else {
            $type_resolutor[$elab_key]= array($elab_value);
          }
        }
        
        
        
        
      }
      
      // dd(array_keys($type_resolutor));
      
      // dd($type_resolutor);
      
      // make the caps in order from the basic account type to the best account type
      $perms = array_flip(array_unique(array_merge(Capability::$GUEST, Capability::$PARTNER, Capability::$CONTENT_MANAGER, Capability::$QUALITY_CONTENT_MANAGER, Capability::$ADMIN)));
      
      $caps = Capability::all();
      
      foreach($caps as $cap){
        $perms[$cap->key] = $cap;
      }
      
      $institutions = Institution::all();


      $viewBag = [
        'pagetitle' => trans('administration.accounts.edit_account_title', ['name' => $user->name]),
        'user' => $user,
        'user_types' => $user_types,
        'institutions' => $institutions,
        'capabilities' => array_values($perms),
        'type_resolutor' => $type_resolutor,
        'edit_enabled' => $auth->user()->id != $user->id,
        'caps' => array_pluck($user->capabilities()->get()->toArray(), 'key')
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
  public function update($id, UserRequest $request)
  {

      $user = User::findOrFail($id);
      
      if($request->has('email')){
          
          $change_mail = $request->get('email');
          $current_mail = $user->email;
          
          $already_exists = User::withTrashed()->fromEmail($change_mail)->first();
          
          if($current_mail != $change_mail && is_null($already_exists)){
              $user->email = $request->get('email');
          }
          else if($current_mail != $change_mail && !is_null($already_exists)){
              return redirect()->back()->withInput()->withErrors([
                    'email' => 'The Email is already in use, please specifiy a different email address'
                ]);
          }
          
          
      }

      if($user->name != $request->get('name')){
          $user->name = $request->get('name');
      }
      
      if($user->getInstitution() != $request->get('institution')){
          $user->institution_id = $request->get('institution');
      }
      
      


      $user->save();

      if($request->has('capabilities')){

        $current_submitted = $request->get('capabilities');
        $current_saved = array_pluck($user->capabilities()->get()->toArray(), 'key');
        
        \DB::transaction(function() use($current_saved, $current_submitted, $user) {
            
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
  public function destroy($id)
  {
      $user = User::findOrFail($id);

      $user->delete();

      return redirect()->route('administration.users.index')->with([
            'flash_message' => trans('administration.accounts.disabled_msg', ['name' => $user->name])
        ]);
  }




    public function remove($id)
    {
        return $this->destroy($id);
    }


    public function restore($id)
    {
        $user = User::onlyTrashed()->where('id', $id)->first();

        $user->restore();

        return redirect()->route('administration.users.index')->with([
              'flash_message' => trans('administration.accounts.enabled_msg', ['name' => $user->name])
          ]);
    }
    
    /**
      Sends the reset password link from the Administration interface
    */
    public function resetPassword($id)
    {
      
      try{
        $user = User::findOrFail($id);

        $view = \Password::sendResetLink(array('email' => $user->email, 'id' => $user->id), function($m, $user, $token){
            $m->subject(trans('mail.password_reset_subject'));
        });
        
        if($view == PasswordBrokerContract::INVALID_USER){
          return redirect()->back()->withErrors([
	            'error' => trans('administration.accounts.reset_not_sent_invalid_user', ['email' => $user->email])
	        ]);
        }
        else if($view == PasswordBrokerContract::RESET_LINK_SENT){
          return redirect()->back()->with([
              'flash_message' => trans('administration.accounts.reset_sent', ['name' => $user->name, 'email' => $user->email])
          ]);
        }
        else {
          return redirect()->back()->withErrors([
	            'error' => trans('administration.accounts.reset_not_sent', ['email' => $user->email, 'error' => ''])
	        ]);
        }

        
      }catch(\Exception $ex){
          
          \Log::error('Password reset from admin interface error', ['error' => $ex]);
          
        return redirect()->back()->withErrors([
	            'error' => trans('administration.accounts.reset_not_sent', ['email' => $id, 'error' => $ex->getMessage()])
	        ]);
      }
    }

}
