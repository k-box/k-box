<?php

namespace KBox\Http\Controllers\Administration;

use KBox\User;
use KBox\Http\Controllers\Controller;
use KBox\Institution;
use Illuminate\Contracts\Auth\Guard as AuthGuard;

/**
 * Check and create the institutions reference
 */
class InstitutionsController extends Controller
{

  /*
  |--------------------------------------------------------------------------
  | Institutions Controller
  |--------------------------------------------------------------------------
  |
  | Handle Institutions from the admins to the users.
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

        $this->middleware('capabilities');
    }

    /**
     * Show the list of ...
     *
     * @return Response
     */
    public function index(AuthGuard $auth)
    {
        $institutions = Institution::all();

        $data = ['institutions' => $institutions, 'pagetitle' => trans('administration.menu.institutions')];
    
        $data['current_institution'] = config('dms.institutionID');

        return view('administration.institutions.index', $data);
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
}
