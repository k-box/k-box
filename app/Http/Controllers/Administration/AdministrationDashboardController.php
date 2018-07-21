<?php

namespace KBox\Http\Controllers\Administration;

use Exception;
use KBox\Http\Controllers\Controller;
use KBox\User;
use KBox\Option;

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
        $config_errors = [];

        if (! Option::isMailEnabled()) {
            $notices[] = trans('notices.mail_not_configured', ['url' => route('administration.mail.index')]);
        }
    
        if (! Option::areContactsConfigured()) {
            $notices[] = trans('notices.contacts_not_configured', ['url' => route('administration.identity.index')]);
        }
      
        try {
            Option::copyright_default_license();

            if (! Option::isDefaultLicenseConfigured()) {
                $notices[] = trans('notices.default_license_not_set', ['url' => route('administration.licenses.index')]);
            }
        
            if (! Option::areAvailableLicensesConfigured()) {
                $notices[] = trans('notices.available_licenses_not_set', ['url' => route('administration.licenses.index')]);
            }
        } catch (Exception $ex) {
            $config_errors[] = trans('notices.license_configuration_error');
        }
    
        return view('administration.administration', [
        'notices' => $notices,
        'error_notices' => $config_errors,
        ]);
    }
}
