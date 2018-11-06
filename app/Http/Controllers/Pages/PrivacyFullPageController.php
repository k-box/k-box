<?php

namespace KBox\Http\Controllers\Pages;

use KBox\Pages\Page;
use KBox\Http\Controllers\Controller;

class PrivacyFullPageController extends Controller
{
    public function index()
    {
        $page = Page::find(Page::PRIVACY_POLICY_FULL, app()->getLocale()) ?? Page::find(Page::PRIVACY_POLICY_FULL, config('app.fallback_locale'));
        
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
