<?php

namespace KBox\Http\Controllers;

use KBox\Consent;
use KBox\Consents;
use KBox\HomeRoute;
use Illuminate\Http\Request;

class OthersConsentDialogController extends Controller
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

        if (Consent::isGiven(Consents::NOTIFICATION, $user) || Consent::isGiven(Consents::STATISTIC, $user)) {
            return redirect()->to(HomeRoute::get($user));
        }

        return view('consents.others', [
            'pagetitle' => trans('consent.privacy.dialog_title'),
            'skip_to' => HomeRoute::get($user)
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

        return redirect()->to(HomeRoute::get($user));
    }
}
