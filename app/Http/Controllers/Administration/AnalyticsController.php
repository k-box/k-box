<?php

namespace KBox\Http\Controllers\Administration;

use Log;
use Exception;
use KBox\Option;
use Illuminate\Support\Str;
use KBox\Support\Analytics\Analytics;
use KBox\Http\Controllers\Controller;
use KBox\Http\Requests\AnalyticsSaveRequest;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use Illuminate\Support\Facades\Gate;

class AnalyticsController extends Controller
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
            'pagetitle' => trans('administration.menu.analytics'),
            Analytics::ANALYTICS_TOKEN => Analytics::token(),
            Analytics::ANALYTICS_SERVICE => Analytics::serviceName(),
            'analytics_domain' => Analytics::configuration('domain'),
            'available_services' => collect(config('analytics.services'))->keys()->toArray(),
        ];

        return view('administration.analytics.index', $data);
    }
  
    public function update(AuthGuard $auth, AnalyticsSaveRequest $request)
    {
        Gate::authorize('manage-kbox');

        try {
            if ($request->has(Analytics::ANALYTICS_TOKEN) && ! empty($request->input(Analytics::ANALYTICS_TOKEN, null))) {
                Option::put(Analytics::ANALYTICS_TOKEN, e($request->input(Analytics::ANALYTICS_TOKEN, null)));
            } else {
                // disable it
                Option::remove(Analytics::ANALYTICS_TOKEN);
            }
            
            if ($request->has(Analytics::ANALYTICS_SERVICE) && ! empty($request->input(Analytics::ANALYTICS_SERVICE, null))) {
                Option::put(Analytics::ANALYTICS_SERVICE, e($request->input(Analytics::ANALYTICS_SERVICE, null)));
            }

            if ($request->has('analytics_domain') && ! empty($request->input('analytics_domain', null))) {
                $domain = e($request->input('analytics_domain', null));

                if (! Str::startsWith($domain, 'http://') && ! Str::startsWith($domain, 'https://')) {
                    $domain = 'https://'.$domain;
                }

                Option::put(Analytics::ANALYTICS_CONFIGURATION, json_encode(['domain' => Str::finish($domain, '/')]));
            } else {
                Option::remove(Analytics::ANALYTICS_CONFIGURATION);
            }

            return redirect()->route('administration.analytics.index')->with([
              'flash_message' => trans('administration.analytics.saved')
            ]);
        } catch (Exception $ex) {
            Log::error('Analytics settings save error', ['error' => $ex, 'request' => $request->all()]);
        
            return redirect()->back()->withInput()->withErrors([
              'error' => trans('administration.analytics.save_error', ['error' => $ex->getMessage()])
            ]);
        }
    }
}
