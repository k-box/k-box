<?php

namespace KBox\Http\Controllers\Administration\DocumentLicenses;

use KBox\Option;
use KBox\DocumentDescriptor;
use Illuminate\Support\Facades\Gate;
use KBox\Jobs\ReindexDocument;
use Illuminate\Validation\Rule;
use KBox\Http\Controllers\Controller;
use Klink\DmsAdapter\KlinkVisibilityType;
use OneOffTech\Licenses\Contracts\LicenseRepository;

class DefaultDocumentLicensesController extends Controller
{
    private $licenses = null;

    public function __construct(LicenseRepository $licenses)
    {
        $this->middleware('auth');
        
        $this->licenses = $licenses;
    }

    public function update()
    {
        Gate::authorize('manage-kbox');
        
        $selectable_licenses = Option::copyright_available_licenses()->pluck('id')->toArray();
 
        $this->validate(request(), [
            'default_license' => [
                'required',
                'string',
                Rule::in($selectable_licenses)
            ],
            'apply_to' => 'nullable|sometimes|required|in:all,previous',
        ]);

        $selected_license = $this->licenses->find(request()->input('default_license', null));

        Option::put(Option::COPYRIGHT_DEFAULT_LICENSE, $selected_license->id);

        if (request()->input('apply_to', null) === 'previous') {
            DocumentDescriptor::whereNull('copyright_usage')->chunk(100, function ($without_license) use ($selected_license) {
                $without_license->each(function ($document) use ($selected_license) {
                    $document->copyright_usage = $selected_license->id;
                    $document->save();

                    dispatch(new ReindexDocument($document, KlinkVisibilityType::KLINK_PRIVATE));
                });
            });
        } elseif (request()->input('apply_to', null) === 'all') {
            DocumentDescriptor::whereNull('copyright_usage')->orWhere('copyright_usage', '!=', $selected_license->id)->chunk(100, function ($documents) use ($selected_license) {
                $documents->each(function ($document) use ($selected_license) {
                    $document->copyright_usage = $selected_license->id;
                    $document->save();

                    dispatch(new ReindexDocument($document, KlinkVisibilityType::KLINK_PRIVATE));
                });
            });
        }

        return redirect()->back()->with([
            'flash_message' => trans('administration.documentlicenses.default.saved', ['title' => $selected_license->title])
        ]);
    }
}
