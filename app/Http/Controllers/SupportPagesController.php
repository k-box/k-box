<?php

namespace KBox\Http\Controllers;

class SupportPagesController extends Controller
{
    /**
     * Renders the Help - Frequently Asked Questions page
     */
    public function help()
    {
        $fallback = base_path('resources/assets/pages/en/help.md');
        $path = base_path('resources/assets/pages/'.app()->getLocale().'/help.md');
        
        $help_file_content = file_get_contents(@is_file($path) ? $path : $fallback);

        return view('static.page', [
            'pagetitle' => trans('pages.help'),
            'page_content' => $help_file_content]);
    }

    /**
     * Renders the browser update explanation page
     */
    public function browserupdate()
    {
        return view('static.browserupdate');
    }
}
