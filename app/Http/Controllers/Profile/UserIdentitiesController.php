<?php

namespace KBox\Http\Controllers\Profile;

use Illuminate\Http\Request;
use KBox\Facades\Identity as FacadesIdentity;
use KBox\Http\Controllers\Controller;
use KBox\Identity;

class UserIdentitiesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $available = FacadesIdentity::enabledProviders();

        abort_if(empty($available), 404);

        $identities = $request->user()->identities;

        return view('profile.identities', [
            'pagetitle' => trans('profile.identities'),
            'breadcrumb_current' => trans('profile.identities'),
            'identities' => $identities,
            'enabled' => $identities->pluck('provider'),
            'availableProviders' => $available,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \KBox\Identity  $identity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Identity $identity)
    {
        $available = FacadesIdentity::enabledProviders();

        abort_if(empty($available), 404);
        
        $this->authorize('delete', $identity);

        $identity->delete();

        if ($request->wantsJson()) {
            return response()->json($identity);
        }

        return redirect()->route('profile.identities.index')->with([
            'flash_message' => trans('identities.removed', ['provider' => $identity->provider]),
        ]);
    }
}
