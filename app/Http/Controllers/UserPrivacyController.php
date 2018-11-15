<?php

namespace KBox\Http\Controllers;

use KBox\Consent;
use Illuminate\Http\Request;

/**
 * User Privacy Controller.
 *
 * User privacy and consent page under its profile
 */
class UserPrivacyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \KBox\Consent  $consent
     * @return \Illuminate\Http\Response
     */
    public function show(Consent $consent)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \KBox\Consent  $consent
     * @return \Illuminate\Http\Response
     */
    public function edit(Consent $consent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \KBox\Consent  $consent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Consent $consent)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \KBox\Consent  $consent
     * @return \Illuminate\Http\Response
     */
    public function destroy(Consent $consent)
    {
        //
    }
}
