<?php

namespace KlinkDMS\Http\Controllers;

class SupportPagesController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Renders the terms of use page
     */
    public function terms()
    {
        $fallback = base_path('resources/assets/pages/en/terms-of-use.md');
        $path = base_path('resources/assets/pages/'.app()->getLocale().'/terms-of-use.md');
        
        $file_content = file_get_contents(@is_file($path) ? $path : $fallback);

        $page_text = \Markdown::convertToHtml($file_content);

        return view('static.page', ['pagetitle' => trans('pages.service_policy'), 'page_content' => $page_text]);
    }

    /**
     * Renders the privacy policy page
     */
    public function privacy()
    {
        $help_file_content = file_get_contents(base_path('resources/assets/pages/privacy.md'));

        $page_text = \Markdown::convertToHtml($help_file_content);

        return view('static.page', ['page_title' => trans('pages.privacy'), 'page_content' => $page_text]);
    }

    /**
     * Renders the Help - Frequently Asked Questions page
     */
    public function help()
    {
        $fallback = base_path('resources/assets/pages/en/help.md');
        $path = base_path('resources/assets/pages/'.app()->getLocale().'/help.md');
        
        $help_file_content = file_get_contents(@is_file($path) ? $path : $fallback);

        $page_text = \Markdown::convertToHtml($help_file_content);

        return view('static.page', [
            'pagetitle' => trans('pages.help'),
            'page_content' => $page_text]);
    }

    /**
     * Renders the help of the document import
     */
    public function importhelp()
    {
        $help_file_content = file_get_contents(base_path('resources/assets/pages/import.md'));

        $page_text = \Markdown::convertToHtml($help_file_content);

        return view('static.page', ['page_title' => trans('pages.help'), 'page_content' => $page_text]);
    }

    /**
     * Renders the browser update explanation page
     */
    public function browserupdate()
    {
        return view('static.browserupdate');
    }
}
