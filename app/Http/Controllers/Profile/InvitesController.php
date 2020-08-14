<?php

namespace KBox\Http\Controllers\Profile;

use KBox\Invite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use KBox\Auth\Registration;
use KBox\Http\Controllers\Controller;

class InvitesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');

        $this->checkIfEnabled();
    }

    private function checkIfEnabled()
    {
        if (! (app()->runningInConsole() && app()->environment('local'))) {
            abort_unless(Registration::isEnabled(), 404);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $invites = Invite::mine($request->user())->get();

        return view('invites.index', [
            'invites' => $invites,
            'page_title' => trans('invite.label'),
            'breadcrumb_current' => trans('invite.label'),
            'expiration_period' => config('invites.expiration'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Invite::class);

        return view('invites.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Invite::class);

        $validData = $this->validate($request, [
            'email' => 'required|string|email|unique:invites,email',
        ]);

        $invite = Invite::generate($request->user(), $validData['email']);

        return redirect()->route('profile.invite.index')->with([
            'flash_message' => trans('invite.created', ['email' => e($validData['email'])]),
            'new_invite' => $invite->uuid,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \KBox\Invite  $invite
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invite $invite)
    {
        $this->authorize('delete', $invite);

        $invite->delete();

        return redirect()->route('profile.invite.index')->with([
            'flash_message' => trans('invite.deleted', ['email' => $invite->email]),
        ]);
    }
}
