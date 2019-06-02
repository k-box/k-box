<?php

namespace KBox\Http\Controllers;

use Validator;
use KBox\PersonalExport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use KBox\Jobs\PreparePersonalExportJob;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $exports = PersonalExport::ofUser($request->user())->orderBy('created_at', 'desc')->get();

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
    public function show(PersonalExport $export)
    {
        $disk = Storage::disk(config('personal-export.disk'));

        if (! $disk->exists($export->name) || $export->isExpired()) {
            abort(404);
        }

        $downloadHeaders = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type' => 'application/zip',
            'Content-Length' => $disk->size($export->name),
            'Content-Disposition' => 'attachment; filename="'.$export->name.'"',
            'Pragma' => 'public',
        ];

        return new StreamedResponse(function () use ($export, $disk, $downloadHeaders) {
            $stream = $disk->readStream($export->name);

            fpassthru($stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        }, Response::HTTP_OK, $downloadHeaders);
    
    }


}
