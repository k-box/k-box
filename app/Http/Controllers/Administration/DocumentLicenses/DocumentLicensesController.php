<?php

namespace KBox\Http\Controllers\Administration\DocumentLicenses;

use KBox\Option;
use KBox\DocumentDescriptor;
use KBox\Http\Controllers\Controller;
use OneOffTech\Licenses\Contracts\LicenseRepository;

/**
 * licenses settings page
 *
 * enable to see the currently configured default license
 * (for new documents) and the available usable licenses
 */
class DocumentLicensesController extends Controller
{
    private $licenses = null;

    public function __construct(LicenseRepository $licenses)
    {
        $this->middleware('auth');

        $this->middleware('capabilities');

        $this->licenses = $licenses;
    }

    public function index()
    {
        $this->licenses = app()->make(LicenseRepository::class);

        $selected_licenses = Option::copyright_available_licenses();

        $default_license = Option::copyright_default_license();
        
        $settings_are_explicitly_configured = Option::isDefaultLicenseConfigured() || Option::areAvailableLicensesConfigured();

        $without_license_count = DocumentDescriptor::whereNull('copyright_usage')->count();

        return view('administration.documentlicenses.index', [
            'pagetitle' => trans('administration.menu.licenses'),
            'licenses' => $this->licenses->all(),
            'selected_licenses' => $selected_licenses,
            'default_license' => $default_license,
            'settings_are_explicitly_configured' => $settings_are_explicitly_configured,
            'documents_without_license' => $without_license_count
        ]);
    }
}
