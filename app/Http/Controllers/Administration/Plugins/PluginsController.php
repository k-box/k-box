<?php

namespace KBox\Http\Controllers\Administration\Plugins;

use KBox\Flags;
use Illuminate\Http\Request;
use KBox\Http\Controllers\Controller;

class PluginsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('administration.plugins.index', [
            'pagetitle' => trans('plugins.page_title')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Flags::enable(Flags::PLUGIN_GEO);
        
        return redirect()->route('administration.plugins.index')->with([
            'flash_message' => trans('plugins.messages.enabled')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Flags::disable(Flags::PLUGIN_GEO);
        
        return redirect()->route('administration.plugins.index')->with([
            'flash_message' => trans('plugins.messages.disabled')
        ]);
    }
}
