<?php

namespace KBox\Http\Controllers;

use KBox\PersonalExport;
use Validator;
use Illuminate\Http\Request;
use KBox\Jobs\PreparePersonalExportJob;

class PersonalExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    /**
     * Display the personal data export page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $exports = PersonalExport::ofUser($request->user())->get();

        return view('profile.data-export', [
            'exports' => $exports
        ]);
    }

    /**
     * Trigger the creation of a new personal export.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $waiting = PersonalExport::ofUser($user)->notExpired()->pending()->where('created_at', '<=', now()->addMinutes(60))->count();

        Validator::make(
            ['time' => (int)$waiting],
            [
                'time' => 'required|lt:1',
            ], 
            [
                'lt' => trans('profile.data-export.wait_until', ['minutes' => 60]),
            ])
            ->validate();

        $export_request = PersonalExport::requestNewExport($user);

        dispatch(new PreparePersonalExportJob($export_request));

        return redirect()->route('profile.data-export.index')->with([
            'flash_message' => trans('profile.data-export.triggered')
        ]);
    }

    /**
     * Download the generated personal data export
     *
     * @param  \KBox\PersonalExport  $personalExport
     * @return \Illuminate\Http\Response
     */
    public function show(PersonalExport $personalExport)
    {
        //
    }


}
