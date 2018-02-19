<?php

namespace KBox\Http\Controllers;

use KBox\Option;
use Illuminate\Http\Request;
use OneOffTech\Licenses\Contracts\LicenseRepository;

class LicensesHelpController extends Controller
{
    /**
     * Shows the short description for all the licenses.
     *
     */
    public function index(Request $request)
    {
        $licenses = collect();
        
        if ($request->input('filter') === 'all') {
            $licenses = app()->make(LicenseRepository::class)->all();
        } else {
            $licenses = Option::copyright_available_licenses();
        }

        $data = [
            'licenses' => $licenses,
            'pagetitle' => trans('documents.edit.license_choose_help_button'),
        ];

        if ($request->ajax()) {
            return view('panels.licensehelp', $data);
        }

        return view('static.licenseshelp', $data);
    }
}
