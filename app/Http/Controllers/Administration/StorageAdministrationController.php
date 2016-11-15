<?php namespace KlinkDMS\Http\Controllers\Administration;

use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\Http\Requests\UserRequest;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use KlinkDMS\User;
use KlinkDMS\Option;
use Klink\DmsDocuments\DocumentsService;
use KlinkDMS\Jobs\ReindexAll;
use Illuminate\Foundation\Bus\DispatchesCommands;

/**
 * Controller
 */
class StorageAdministrationController extends Controller {

  use DispatchesCommands;

  /*
  |--------------------------------------------------------------------------
  | Storage Management Page Controller
  |--------------------------------------------------------------------------
  |
  | This controller respond to actions for the "storage administration page".
  |
  */


  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct() {

    $this->middleware('auth');

    $this->middleware('capabilities');

  }

  public function getIndex(DocumentsService $service)
  {


    $status = $service->getStorageStatus(true);


    $raw = $status['raw_data'];

    // specific doc folder size
    // 
    // if app and doc folder are on different disks show two or more disks

    // dd($status);

    $app_dirname = dirname($status['app_folder']);
    $docs_dirname = dirname(dirname($status['docs_folder']));

    $disks = array();

    if($raw['total_app'] !== $raw['total_docs']){
      // we have two disks

      $used_perc = round($raw['used_docs']/$raw['total_docs']*100);
      $used_app_perc = round($raw['used_app']/$raw['total_app']*100);


      $disks[] = array('name' => trans('administration.storage.disk_number', ['number' => 1]), 'type' => trans('administration.storage.disk_type_docs'),

          'free' => DocumentsService::human_filesize($raw['free_docs']),
          'used' => DocumentsService::human_filesize($raw['used_docs']),
          'total' => DocumentsService::human_filesize($raw['total_docs']),

          'free_percentage' => round($raw['free_docs']/$raw['total_docs']*100),
          'used_percentage' => $used_perc,
          'level' => ($used_perc > 50) ? ($used_perc > 80 ? 'critical' : 'medium') : 'ok',
          // 'total' => DocumentsService::human_filesize($raw['total_docs']),
        );

      $disks[] = array('name' => trans('administration.storage.disk_number', ['number' => 2]), 'type' => trans('administration.storage.disk_type_main'),

          'free' => DocumentsService::human_filesize($raw['free_app']),
          'used' => DocumentsService::human_filesize($raw['used_app']),
          'total' => DocumentsService::human_filesize($raw['total_app']),

          'free_percentage' => round($raw['free_app']/$raw['total_app']*100),
          'used_percentage' => $used_perc,
          'level' => ($used_app_perc > 50) ? ($used_app_perc > 80 ? 'critical' : 'medium') : 'ok',
        );

    }
    else {
      // Disk 1 - document + app
      // 
      $used_perc = round($raw['used_docs']/$raw['total_docs']*100);
      $disks[] = array('name' => trans('administration.storage.disk_number', ['number' => 1]), 'type' => trans('administration.storage.disk_type_all'), 

          'free' => DocumentsService::human_filesize($raw['free_docs']),
          'used' => DocumentsService::human_filesize($raw['used_docs']),
          'total' => DocumentsService::human_filesize($raw['total_docs']),

          'free_percentage' => round($raw['free_docs']/$raw['total_docs']*100),
          'used_percentage' => $used_perc,
          'level' => ($used_perc > 50) ? ($used_perc > 80 ? 'critical' : 'medium') : 'ok',
        );
    }


    
    // Option::put('dms.reindex.pending', $count);
    // Option::put('dms.reindex.completed', 0);
    // Option::put('dms.reindex.total', $count);

    $reindex = $this->getReindexExecutionStatus();


    if(isset($reindex['executing']) && ($reindex['executing'] == 'false' || !$reindex['executing'])){

      $reindex = null;
    }

    return view('administration.storage', [
        'pagetitle' => trans('administration.menu.storage'), 
        'status' => $status,
        'disks' => $disks,
        'reindex' => $reindex,
        'is_naming_policy_active' => Option::option('dms.namingpolicy.active', false)

      ]);
  }


  private function getReindexExecutionStatus(){

    $items = Option::sectionAsArray('dms.reindex');

    if(isset($items['dms']['reindex'])){
      $items = $items['dms']['reindex'];
    }

    $defaults = array(
        'status' => trans('administration.storage.reindexing_status', ['number' => (isset($items['total'])) ? $items['total'] : 0]),
        'pending' => 0,
        'completed' => 0,
        'total' => 0,
        'progress_percentage' => (isset($items['total']) && isset($items['completed'])) ? round(((int)$items['completed']/(int)$items['total'])*100) : 0,
      );

    if($defaults['progress_percentage'] == 100){
      $items['status'] = trans('administration.storage.reindexing_status_completed');
    }

    // dd(compact('items', 'defaults'));

    return array_merge($defaults, $items);
  }

  /**
   * Get the reindex all procedure status
   * @return [type] [description]
   */
  public function getReindexAll()
  {
    
    $reindex = $this->getReindexExecutionStatus();

    return response()->json($reindex);
  }

  /**
   * Start the Reindex All procedure
   * @return [type] [description]
   */
  public function postReindexAll()
  {

    $all_id = DocumentDescriptor::all(array('id'))->map(function($el){
      return $el->id;
    });

    $count = $all_id->count();

    Option::put('dms.reindex.executing', true);
    Option::put('dms.reindex.pending', $count);
    Option::put('dms.reindex.completed', 0);
    Option::put('dms.reindex.total', $count);
    Option::remove('dms.reindex.error');

    $this->dispatch(
        new ReindexAll(\Auth::user(), $all_id->toArray())
    );

    return response()->json([
      'status' => trans('administration.storage.reindexing_status', ['number' => $count]),
      'pending' => $count,
      'completed' => 0,
      'total' => $count,
      'progress_percentage' => 0,
    ]);

  }


  /**
   * Save the configuration of the naming policy option
   */
  public function postNaming()
  {
    # code...

    

    if(\Request::has('activate')){

      $activate = !!\Request::input('activate', null);

      if(!is_null($activate) && $activate){

        Option::put('dms.namingpolicy.active', true);

        return redirect()->route('administration.storage.index')->with([
              'flash_message' => trans('administration.storage.naming_policy_msg_activated')
          ]);

      }
      else if(!is_null($activate) && !$activate) {

        Option::put('dms.namingpolicy.active', false);

        return redirect()->route('administration.storage.index')->with([
              'flash_message' => trans('administration.storage.naming_policy_msg_deactivated')
          ]);

      }

      

      
    }
    
    return redirect()->route('administration.storage.index');
    
  }

}
