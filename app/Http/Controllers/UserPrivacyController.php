<?php

namespace KBox\Http\Controllers;

use KBox\Consent;
use KBox\Consents;
use Illuminate\Http\Request;

/**
 * User Privacy Controller.
 *
 * User privacy and consent page under its profile
 */
class UserPrivacyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagetitle = trans('profile.privacy.privacy');
        $user = $request->user();

        $user_consents = $user->consents()->get();

        $consent_privacy = $user_consents->firstWhere('consent_topic', Consents::PRIVACY);
        $consent_notification = $user_consents->firstWhere('consent_topic', Consents::NOTIFICATION);
        $consent_statistics = $user_consents->firstWhere('consent_topic', Consents::STATISTIC);

        $consent_privacy_given = ! is_null($consent_privacy);
        $consent_privacy_activity = $this->getStatusMessageForConsent($consent_privacy);

        $consent_notification_given = ! is_null($consent_notification);
        $consent_notification_activity = $this->getStatusMessageForConsent($consent_notification);

        $consent_statistics_given = ! is_null($consent_statistics);
        $consent_statistics_activity = $this->getStatusMessageForConsent($consent_statistics);

        return view('profile.privacy', compact(
            'pagetitle',
            'user',
            'consent_privacy_given',
            'consent_privacy_activity',
            'consent_notification_given',
            'consent_notification_activity',
            'consent_statistics_given',
            'consent_statistics_activity'
        ));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'notifications' => 'sometimes|required|boolean',
            'statistics' => 'sometimes|required|boolean',
        ]);

        $user = $request->user();

        if ($request->has('notifications')) {
            $action = (integer)$request->input('notifications', 0) === 1 ? 'agree' : 'withdraw';

            Consent::{$action}($user, Consents::NOTIFICATION);
        }

        if ($request->has('statistics')) {
            $action = (integer)$request->input('statistics', 0) === 1 ? 'agree' : 'withdraw';

            Consent::{$action}($user, Consents::STATISTIC);
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return redirect()->route('profile.privacy.index');
    }

    private function getStatusMessageForConsent(?Consent $consent)
    {
        if (! $consent) {
            return '';
        }

        return trans('profile.privacy.activity.consent_given', ['date' => optional($consent)->getCreatedAt()]);
    }
}
