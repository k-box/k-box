<?php namespace KlinkDMS\Http\Controllers\Administration;

use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\Http\Requests\UserRequest;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use KlinkDMS\User;

/**
 * Controller
 */
class LanguageAdministrationController extends Controller {

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct() {

    $this->middleware('auth');

    $this->middleware('capabilities');

  }

  public function getIndex(\Klink\DmsDocuments\DocumentsService $storage)
  {

    $languages = \Cache::get('dms_languages', function() use($storage) { 
      $path = base_path('resources/lang/');

      $directories = $storage->directories(base_path('resources/lang/'));

      return array_map(function($el){ return basename($el); }, $directories);
    });


    

    // dd(compact('path', 'directories', ''));


    return view('administration.languages', ['pagetitle' => trans('administration.menu.language'), 'languages' => $languages]);
  }

}
