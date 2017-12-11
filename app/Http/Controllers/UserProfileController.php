<?php

namespace KBox\Http\Controllers;

use KBox\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Guard as Auth;
use KBox\Http\Requests\ProfileUpdateRequest;
use KBox\Http\Requests\UserOptionUpdateRequest;

class UserProfileController extends Controller
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
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Auth $auth)
    {
        $user = $auth->user();

        $stars_count = $user->starred()->count();
        $shares_count = $user->shares()->count();
        $documents_count = $user->documents()->count();
        $collections_count = $user->groups()->count();

        $pagetitle = trans('profile.page_title', ['name' => $user->name]);
        
        $language = $user->optionLanguage('en');

        
        return view('profile.user', compact('pagetitle', 'user', 'stars_count', 'shares_count', 'documents_count', 'collections_count', 'language'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Auth $auth, ProfileUpdateRequest $request)
    {
        // TODO: this should not be a store(), but an update() as the user is already created
        $user = $auth->user();

        if ($request->get('_change') === 'mail') {
            $user->email = $request->get('email');
            $message = trans('profile.messages.mail_changed');
        } elseif ($request->get('_change') === 'pass') {
            $user->password = \Hash::make($request->get('password'));
            $message = trans('profile.messages.password_changed');
        } elseif ($request->get('_change') === 'info') {
            $user->name = $request->get('name');
            $user->organization_name = e($request->get('organization_name', ''));
            $user->organization_website = e($request->get('organization_website', ''));
            $message = trans('profile.messages.info_changed');
        } elseif ($request->get('_change') === 'language') {
            $user->setOption(User::OPTION_LANGUAGE, $request->get(User::OPTION_LANGUAGE));
            
            $message = trans('profile.messages.language_changed');
        }

        $user->save();

        return redirect()->route('profile.index')->with([
            'flash_message' => $message
        ]);
    }

    /**
     * used for Update user options (like the type of view of the lists)
     */
    public function update(Auth $auth, UserOptionUpdateRequest $request)
    {
        // TODO: move this to /profile/options or me/options store()
        $user = $auth->user();

        \Log::info('Updating user options', ['params' =>$request->all()]);

        if ($request->has(User::OPTION_LIST_TYPE)) {
            $user->setOption(User::OPTION_LIST_TYPE, $request->get(User::OPTION_LIST_TYPE));

            $user->save();
        }
        
        if ($request->has(User::OPTION_LANGUAGE)) {
            $user->setOption(User::OPTION_LANGUAGE, $request->get(User::OPTION_LANGUAGE));

            $user->save();
        }
        
        if ($request->has(User::OPTION_TERMS_ACCEPTED)) {
            $user->setOption(User::OPTION_TERMS_ACCEPTED, filter_var($request->get(User::OPTION_TERMS_ACCEPTED), FILTER_VALIDATE_BOOLEAN));

            $user->save();
        }

        if ($request->has(User::OPTION_PERSONAL_IN_PROJECT_FILTERS)) {
            $user->setOption(User::OPTION_PERSONAL_IN_PROJECT_FILTERS, filter_var($request->get(User::OPTION_PERSONAL_IN_PROJECT_FILTERS), FILTER_VALIDATE_BOOLEAN));

            $user->save();
        }

        if ($request->has(User::OPTION_ITEMS_PER_PAGE)) {
            $user->setOptionItemsPerPage($request->get(User::OPTION_ITEMS_PER_PAGE));

            $user->save();
        }
        
        if ($request->wantsJson()) {
            return new JsonResponse(['status' => 'ok'], 200);
        }
        
        response(200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        $stars_count = $user->starred()->count();
        $shares_count = $user->shares()->count();
        $documents_count = $user->documents()->count();
        $collections_count = $user->groups()->count();

        $pagetitle = trans('profile.page_title', ['name' => $user->name]);

        return view('profile.user', compact('pagetitle', 'user', 'stars_count', 'shares_count', 'documents_count', 'collections_count'));
    }
}
