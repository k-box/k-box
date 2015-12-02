<?php namespace KlinkDMS\Http\Controllers\Administration;

use KlinkDMS\Capability;
use KlinkDMS\DocumentDescriptor;
use KlinkDMS\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use KlinkDMS\User;

class AdministrationDashboardController extends Controller {

  /*
  |--------------------------------------------------------------------------
  | Administration Page Controller
  |--------------------------------------------------------------------------
  |
  | This controller renders the "administrator dashboard page".
  |
  */


  /**
   * [$adapter description]
   * @var \Klink\DmsAdapter\KlinkAdapter
   */
  private $adapter = NULL;

  /**
   * [$documents description]
   * @var \Klink\DmsDocuments\DocumentsService
   */
  private $documents = NULL;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(\Klink\DmsAdapter\KlinkAdapter $adapterService, \Klink\DmsDocuments\DocumentsService $documentsService) {

    $this->middleware('auth');

    $this->middleware('capabilities');

    // Only if the user has the correct capabilities

    $this->adapter = $adapterService;

    $this->documents = $documentsService;
  }

  /**
   * Show the application welcome screen to the user.
   *
   * @return Response
   */
  public function index() {

    $public = $this->adapter->getDocumentsCount('public');

    $private = $this->adapter->getDocumentsCount('private');

    $storage = $this->documents->getStorageStatus();

    // dd($storage);

    return view('administration.administration', [
        'document_total' => $public+$private,
        'document_public' => $public,
        'storage_status' => $storage
      ]);
  }


}
