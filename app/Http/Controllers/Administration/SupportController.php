<?php

namespace KBox\Http\Controllers\Administration;

use Log;
use Exception;
use KBox\Option;
use KBox\Support\SupportService;
use KBox\Http\Controllers\Controller;
use KBox\Http\Requests\AnalyticsSaveRequest;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use Illuminate\Support\Facades\Gate;

class SupportController extends Controller
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
  
    public function index(AuthGuard $auth)
    {
        Gate::authorize('manage-kbox');
        
        $data = [
            'pagetitle' => trans('administration.menu.support'),
            SupportService::SUPPORT_TOKEN => SupportService::token(),
        ];

        return view('administration.support.index', $data);
    }
  
    public function update(AuthGuard $auth, AnalyticsSaveRequest $request)
    {
        Gate::authorize('manage-kbox');
        
        try {
            $validatedData = $request->validate([
                SupportService::SUPPORT_TOKEN => 'nullable|sometimes|string',
            ]);

            if ($request->has(SupportService::SUPPORT_TOKEN) && ! empty($request->input(SupportService::SUPPORT_TOKEN, null))) {
                Option::put(SupportService::SUPPORT_TOKEN, e($request->input(SupportService::SUPPORT_TOKEN, null)));
                Option::put(SupportService::SUPPORT_SERVICE, 'uservoice');
            } else {
                // disable it
                Option::remove(SupportService::SUPPORT_TOKEN);
                Option::remove(SupportService::SUPPORT_SERVICE);
            }
            
            return redirect()->route('administration.support.index')->with([
              'flash_message' => trans('administration.support.saved')
            ]);
        } catch (Exception $ex) {
            Log::error('Support settings save error', ['error' => $ex, 'request' => $request->all()]);
        
            return redirect()->back()->withInput()->withErrors([
              'error' => trans('administration.support.save_error', ['error' => $ex->getMessage()])
            ]);
        }
    }
}
