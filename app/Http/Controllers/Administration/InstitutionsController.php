<?php namespace KlinkDMS\Http\Controllers\Administration;

use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\User;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\Http\Requests\CreateMessageRequest;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use KlinkDMS\Institution;
use Klink\DmsAdapter\KlinkAdapter;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerContract;
use KlinkDMS\Http\Requests\InstitutionRequest;


/**
 * Check and create the institutions reference
 */
class InstitutionsController extends Controller {

  /*
  |--------------------------------------------------------------------------
  | Institutions Controller
  |--------------------------------------------------------------------------
  |
  | Handle Institutions from the admins to the users.
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
   * Show the list of ...
   *
   * @return Response
   */
  public function index(AuthGuard $auth) {

    $institutions = Institution::all();

    $data = ['institutions' => $institutions, 'pagetitle' => trans('administration.menu.institutions')];
    
    $data['current_institution'] = \Config::get('dms.institutionID');

    return view('administration.institutions.index', $data);
  }


  /**
   * Show the form for creating a new user.
   *
   * @return Response
   */
  public function create(AuthGuard $auth)
  {
  
      return view('administration.institutions.create', array( 
        'pagetitle' => trans('administration.institutions.create_title'),
      ));
  }

  /**
   * Store a newly created user in storage.
   *
   * @return Response
   */
  public function store(AuthGuard $auth, InstitutionRequest $request)
  {
      try{
        
        $inst = \DB::transaction(function() use($request){
          
            
            $fields = $request->except(array('_token', '_method'));
            
            $fields['type'] = 'Organization';
            $inst = Institution::create($fields);
          
            $this->adapter->saveInstitution($inst);
          
            return $inst;
        });
        
        \Cache::forget('dms_institutions');
        
        return redirect()->route('administration.institutions.index')->with([
            'flash_message' => trans('administration.institutions.saved', ['name' => $inst->name])
        ]);
    
      }catch(\Exception $ex){
        
        \Log::error('Institution create error', ['error' => $ex, 'request' => $request->all()]);
        
        return redirect()->back()->withInput()->withErrors([
	            'error' => trans('administration.institutions.create_error', ['error' => $ex->getMessage()])
	        ]);
      }
  }

  /**
   * Display the specified user.
   *
   * @param  int  $id
   * @return Response
   */
  public function show($id)
  {
    
      $inst = Institution::findOrFail($id);
    
      return view('administration.institutions.show', [
        'pagetitle' => $inst->name,
        'institution' => $inst,
        ]);
  }


  /**
   * Show the form for editing the specified user.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {
    
      $inst = Institution::findOrFail($id);

      return view('administration.institutions.edit', [
        'pagetitle' => trans('administration.institutions.edit_title', ['name' => $inst->name]),
        'institution' => $inst,
        ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id the id of the user to update
   * @param $request The request
   * @return Response
   */
  public function update(InstitutionRequest $request, $id)
  {
    
      try{
        $inst = Institution::findOrFail($id);
        
        \DB::transaction(function() use($request, $inst){
          
            
            $fields = $request->except(array('_token', '_method', 'klink_id'));
              
            foreach($fields as $field_key => $field_value){
              $inst->{$field_key} = $field_value;
            }
          
            $inst->save();
          
            $this->adapter->saveInstitution($inst);
          
        });
        
        return redirect()->route('administration.institutions.index')->with([
            'flash_message' => trans('administration.institutions.saved', ['name' => $inst->name])
        ]);
    
      }catch(\Exception $ex){
        
        \Log::error('Institution update error', ['error' => $ex, 'institution' => $id]);
        
        return redirect()->back()->withInput()->withErrors([
	            'error' => trans('administration.institutions.update_error', ['error' => $ex->getMessage()])
	        ]);
      }
  }

  /**
   * In this case disable the specified user.
   *
   * @param  int  $id the user id to be disabled
   * @return Response
   */
  public function destroy($id)
  {
      $inst = Institution::findOrFail($id);
    
      try{
        
        $no_documents = DocumentDescriptor::where('institution_id', $inst->id)->count() > 0 ? false : true;
        $no_users = User::where('institution_id', $inst->id)->count() > 0 ? false : true;
        
        if($no_documents && $no_users){
          \DB::transaction(function() use($inst){
 
              $this->adapter->deleteInstitution($inst);
              
              $inst->delete();
            
          });
          
          \Cache::forget('dms_institutions');
          
          return redirect()->back()->withInput()->with([
	            'flash_message' => trans('administration.institutions.deleted', ['name' => $inst->name])
	        ]);

        }
        else {
          return redirect()->back()->withInput()->withErrors([
	            'error' => trans('administration.institutions.delete_not_possible', ['name' => $inst->name])
	        ]);
        }
    
      }catch(\Exception $ex){
        
        \Log::error('Institution delete error', ['error' => $ex, 'institution' => $id]);
        
        return redirect()->back()->withInput()->withErrors([
	            'error' => trans('administration.institutions.delete_error', ['error' => $ex->getMessage(), 'name' => $inst->name])
	        ]);
      }
  }

}
