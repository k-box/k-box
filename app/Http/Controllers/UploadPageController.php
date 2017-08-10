<?php

namespace KlinkDMS\Http\Controllers;

class UploadPageController extends Controller
{
    /**
     * Display the tus internal upload page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('upload.index', [
            'pagetitle' => 'New Upload tool',
            'context' => 'uploads',
        ]);
    }
}
