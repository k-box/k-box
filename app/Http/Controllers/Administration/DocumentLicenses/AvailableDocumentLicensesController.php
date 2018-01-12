<?php

namespace KBox\Http\Controllers\Administration\DocumentLicenses;

use KBox\Option;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use KBox\Http\Controllers\Controller;
use OneOffTech\Licenses\Contracts\LicenseRepository;

class AvailableDocumentLicensesController extends Controller
{
    private $licenses = null;

    public function __construct(LicenseRepository $licenses)
    {
        $this->middleware('auth');

        $this->middleware('capabilities');
        
        $this->licenses = $licenses;
    }

    public function update()
    {
        $this->validate(request(), [
            'available_licenses' => [
                'required',
                'array',
                'min:1',
            ],
            'available_licenses.*' => [
                'required',
                'distinct',
                Rule::in($this->licenses->all()->pluck('id')->toArray())
            ]
        ]);

        $selected_licenses = collect(request()->input('available_licenses', []));

        Option::put(Option::COPYRIGHT_AVAILABLE_LICENSES, $selected_licenses->toJson());

        return redirect()->back()->with([
            'flash_message' => trans('administration.documentlicenses.available.saved')
        ]);
    }
}
