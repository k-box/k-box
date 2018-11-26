<?php

namespace KBox\Http\Controllers;

use KBox\User;
use Illuminate\Http\JsonResponse;
use KBox\Http\Requests\UserOptionUpdateRequest;

class UserOptionsController extends Controller
{

    /**
       * Create a new controller instance.
       *
       * @return void
       */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * used for Update user options (like the type of view of the lists)
     */
    public function update(UserOptionUpdateRequest $request)
    {
        $user = $request->user();

        if ($request->has(User::OPTION_LIST_TYPE)) {
            $user->setOption(User::OPTION_LIST_TYPE, $request->get(User::OPTION_LIST_TYPE));
        }

        if ($request->has(User::OPTION_ITEMS_PER_PAGE)) {
            $user->setOptionItemsPerPage($request->get(User::OPTION_ITEMS_PER_PAGE));
        }

        if ($request->has(User::OPTION_PERSONAL_IN_PROJECT_FILTERS)) {
            $user->setOption(User::OPTION_PERSONAL_IN_PROJECT_FILTERS, (bool)$request->get(User::OPTION_PERSONAL_IN_PROJECT_FILTERS));
        }

        $user->save();
        
        if ($request->wantsJson()) {
            return new JsonResponse(['status' => 'ok'], 200);
        }
        
        return redirect()->route('profile.index');
    }
}
