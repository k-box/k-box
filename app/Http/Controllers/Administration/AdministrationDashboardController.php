<?php

namespace KlinkDMS\Http\Controllers\Administration;

use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\User;
use KlinkDMS\Option;

class AdministrationDashboardController extends Controller
{

  /*
  |--------------------------------------------------------------------------
  | Administration Page Controller
  |--------------------------------------------------------------------------
  |
  | This controller renders the "administrator dashboard page".
  |
  */

  /**
   * [$documents description]
   * @var \Klink\DmsDocuments\DocumentsService
   */
  private $documents = null;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(\Klink\DmsDocuments\DocumentsService $documentsService)
  {
      $this->middleware('auth');

      $this->middleware('capabilities');

      $this->documents = $documentsService;
  }

  /**
   * Show the application welcome screen to the user.
   *
   * @return Response
   */
  public function index()
  {
      $notices = [];

      if (! Option::isMailEnabled()) {
          $notices[] = trans('notices.mail_not_configured', ['url' => route('administration.mail.index')]);
      }
    
      if (! Option::areContactsConfigured()) {
          $notices[] = trans('notices.contacts_not_configured', ['url' => route('administration.identity.index')]);
      }
    

      return view('administration.administration', [
        'notices' => $notices,
      ]);
  }
}
