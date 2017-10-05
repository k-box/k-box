<?php

namespace KlinkDMS\Http\Controllers\Administration;

use Illuminate\Http\Request;
use KlinkDMS\Http\Controllers\Controller;
use KlinkDMS\Http\Requests\ContactsSaveRequest;
use KlinkDMS\Option;

/**
 * Manage the K-Box Identity configuration
 */
class IdentityController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Contacts Controller
    |--------------------------------------------------------------------------
    |
    | Handle the configuration for the K-Box identity to be
    | used on the contacts page.
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
    * Show the form for entering the contacts detail
    *
    * @return Response
    */
    public function index()
    {
        $contacts = Option::sectionAsArray('contact');

        $are_contacts_configured = Option::areContactsConfigured();
        
        return view('administration.identity.index', [
            'pagetitle' => trans('administration.identity.page_title'),
            'contacts' => isset($contacts['contact']) ? $contacts['contact'] : $contacts,
            'is_configured' => $are_contacts_configured,
            ]);
    }

    /**
    * Handle the contact details save/update.
    *
    * @return Response
    */
    public function store(ContactsSaveRequest $request)
    {
        try {
            $done = \DB::transaction(function () use ($request) {
                $fields = $request->only(['name','email','phone','website','image','address_street','address_locality','address_country','address_zip']);

                foreach ($fields as $field => $value) {
                    Option::put('contact.'.$field, e($value));
                }

                return true;
            });

            
            return redirect()->route('administration.identity.index')->with([
                'flash_message' => trans('administration.identity.contact_info_updated')
            ]);
        } catch (\Exception $ex) {
            \Log::error('Identity store error', ['error' => $ex, 'request' => $request->all()]);

            return redirect()->back()->withInput()->withErrors([
                'error' => trans('administration.identity.update_error', ['error' => $ex->getMessage()])
            ]);
        }
    }
}
