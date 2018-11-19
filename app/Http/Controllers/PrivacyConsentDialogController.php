<?php

namespace KBox\Http\Controllers;

use KBox\Consent;
use KBox\Consents;
use KBox\HomeRoute;
use KBox\Pages\Page;
use Illuminate\Http\Request;

class PrivacyConsentDialogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the privacy consent dialog.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $user = $request->user();

        if (Consent::isGiven(Consents::PRIVACY, $user)) {
            // if user did agree to the privacy policy
            // the page do not make sense
            return redirect()->to(HomeRoute::get($user));
        }
        
        $legal = Page::find(Page::PRIVACY_POLICY_LEGAL, app()->getLocale()) ?? Page::find(Page::PRIVACY_POLICY_LEGAL, config('app.fallback_locale'));
        $summary = Page::find(Page::PRIVACY_POLICY_SUMMARY, app()->getLocale()) ?? Page::find(Page::PRIVACY_POLICY_SUMMARY, config('app.fallback_locale'));
        
        if (! $legal) {
            // no privacy policy to show, skipping
            return redirect()->to(HomeRoute::get($user));
        }

        return view('consents.privacy', [
            'pagetitle' => trans('consent.privacy.dialog_title'),
            'privacy_content' => $legal->html,
            'summary_content' => $summary ? $summary->html : null
        ]);
    }

    /**
     * Update the user's privacy consent.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $this->validate($request, [
            'agree' => 'required|string|in:privacy'
        ]);

        Consent::agree($user, Consents::PRIVACY);

        return redirect()->to(HomeRoute::get($user));
    }
}
