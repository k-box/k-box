<?php

namespace KBox\Http\Controllers\Pages;

use KBox\Pages\Page;
use KBox\Http\Controllers\Controller;

class TermsPageController extends Controller
{
    public function index()
    {
        $page = Page::find(Page::TERMS_OF_SERVICE, app()->getLocale()) ?? Page::find(Page::TERMS_OF_SERVICE, config('app.fallback_locale'));
        
        if (! $page) {
            abort(404);
        }

        return view('static.page', [
            'pagetitle' => $page->title,
            'pagedescription' => $page->description,
            'page_content' => $page->html
        ]);
    }
}
