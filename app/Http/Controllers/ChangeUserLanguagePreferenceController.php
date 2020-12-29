<?php

namespace KBox\Http\Controllers;

use KBox\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Change the user language preference
 */
class ChangeUserLanguagePreferenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $this->validate($request, [
            User::OPTION_LANGUAGE => 'required|in:en,ru,tg,fr,de,ky'
        ]);

        $user = $request->user();

        $language = $request->get(User::OPTION_LANGUAGE);
        $user->setOption(User::OPTION_LANGUAGE, $language);
        $user->save();

        if ($request->wantsJson()) {
            return new JsonResponse(['status' => 'ok'], 200);
        }
        
        return redirect()->back()->with([
            'flash_message' => trans('profile.messages.language_changed', [], $language)
        ]);
    }
}
