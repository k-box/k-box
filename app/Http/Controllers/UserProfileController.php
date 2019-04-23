<?php

namespace KBox\Http\Controllers;

use Illuminate\Contracts\Auth\Guard as Auth;
use KBox\Http\Requests\ProfileUpdateRequest;

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
        $this->middleware('verified');
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
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        
        $user->name = $request->get('name');
        $user->organization_name = e($request->get('organization_name', ''));
        $user->organization_website = e($request->get('organization_website', ''));

        $user->save();

        return redirect()->route('profile.index')->with([
            'flash_message' => trans('profile.messages.info_changed')
        ]);
    }
}
