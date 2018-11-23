<?php

namespace KBox\Http\Controllers;

use KBox\Consent;
use KBox\Consents;
use KBox\HomeRoute;
use Illuminate\Http\Request;

class NotificationConsentDialogController extends Controller
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
        if (flags()->isDisabled('consent_notifications')) {
            return redirect()->route('consent.dialog.statistic.show');
        }

        $user = $request->user();

        $notification_given = Consent::isGiven(Consents::NOTIFICATION, $user);
        $statistic_given = Consent::isGiven(Consents::STATISTIC, $user);

        if ($notification_given && ! $statistic_given) {
            return redirect()->route('consent.dialog.statistic.show');
        }
        if ($notification_given) {
            return redirect()->to(HomeRoute::get($user));
        }

        return view('consents.notification', [
            'pagetitle' => trans('consent.notifications.dialog_title'),
            'skip_to' => $statistic_given ? HomeRoute::get($user) : route('consent.dialog.statistic.show')
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
        $this->validate($request, [
            'notifications' => 'sometimes|required|boolean',
        ]);

        $user = $request->user();

        if ($request->has('notifications')) {
            $action = (integer)$request->input('notifications', 0) === 1 ? 'agree' : 'withdraw';

            Consent::{$action}($user, Consents::NOTIFICATION);
        }

        if (! Consent::isGiven(Consents::STATISTIC, $user)) {
            return redirect()->route('consent.dialog.statistic.show');
        }

        return redirect()->to(HomeRoute::get($user));
    }
}
