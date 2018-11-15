<?php

namespace KBox\Http\Controllers;

use KBox\Option;

/**
 * Shows the K-Box contact page
 */
class ContactPageController extends Controller
{
    public function index()
    {
        $is_configured = Option::areContactsConfigured();

        $contact_section = Option::sectionAsArray('contact');
        
        $contact = isset($contact_section['contact']) ? $contact_section['contact'] : $contact_section;

        $page_title = trans('pages.contact');
        
        return view('static.contact', compact('contact', 'page_title', 'is_configured'));
    }
}
