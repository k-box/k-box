<?php

namespace KBox\Http\Controllers\Pages;

use KBox\Pages\Page;
use KBox\Http\Controllers\Controller;

class PrivacyLegalPageController extends Controller
{
    public function index()
    {
        $page = Page::find(Page::PRIVACY_POLICY_LEGAL, app()->getLocale()) ?? Page::find(Page::PRIVACY_POLICY_LEGAL, config('app.fallback_locale'));
        
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
