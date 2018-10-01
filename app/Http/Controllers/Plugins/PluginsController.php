<?php

namespace KBox\Http\Controllers\Plugins;

use Illuminate\Http\Request;
use KBox\Plugins\PluginManager;
use KBox\Http\Controllers\Controller;

class PluginsController extends Controller
{
    private $manager;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PluginManager $manager)
    {
        $this->middleware('auth');

        $this->middleware('capabilities');
        
        $this->middleware('flags:plugins');

        $this->manager = $manager;
    }

    /**
     * Show the application welcome screen to the user.
     *
     * @return Response
     */
    public function index()
    {
        return view('plugins.index', [
            'pagetitle' => trans('plugins.page_title'),
            'plugins' => $this->manager->plugins(),
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
        $this->manager->enable($id);
        
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
        $this->manager->disable($id);
        
        return redirect()->route('administration.plugins.index')->with([
            'flash_message' => trans('plugins.messages.disabled')
        ]);
    }
}
