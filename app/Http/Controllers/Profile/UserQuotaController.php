<?php

namespace KBox\Http\Controllers\Profile;

use KBox\UserQuota;
use KBox\Services\Quota;
use Illuminate\Http\Request;
use KBox\Http\Controllers\Controller;
use Jenssegers\Date\Date as LocalizedDate;
use KBox\Jobs\CalculateUserUsedQuota;

class UserQuotaController extends Controller
{
    private $quotaService;

    public function __construct(Quota $quotaService)
    {
        $this->middleware('auth');
        $this->middleware('verified');

        $this->quotaService = $quotaService;
    }

    private function formatDate($date, $full = false)
    {
        if (! $date) {
            return null;
        }
        
        $dt = LocalizedDate::instance($date);

        return $dt->format(trans($full ? 'units.date_format_full' : 'units.date_format'));
    }

    /**
     * Display the user quota page in the profile section.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $userquota = $this->quotaService->user($request->user());

        $scale = range(10, 100, 10);
        
        return view('profile.storage', [

            'threshold' => $userquota->threshold,

            'unlimited' => $userquota->unlimited,
            
            'percentage' => $userquota->used_percentage,
            
            'is_above_threshold' => $userquota->is_above_threshold,
            'is_full' => $userquota->is_full,

            'notification_sent_at' => $this->formatDate($userquota->notification_sent_at),
            'full_notification_sent_at' => $this->formatDate($userquota->full_notification_sent_at),

            'used' => human_filesize($userquota->used),
            'total' => human_filesize($userquota->limit),

            'breadcrumb_current' => trans('profile.storage.title'),
            'pagetitle' => trans('profile.storage.title'),
            
            'scale' => $scale,
        ]);
    }

    /**
     * Update the user quota preferences.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \KBox\UserQuota  $userQuota
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $userquota = $this->quotaService->user($request->user());

        $valid_data = $this->validate($request, [
            'threshold' => 'required|filled|numeric|integer|min:5|max:98',
        ]);

        if ($userquota->unlimited) {
            return redirect()->route('profile.storage.index')->withErrors([
                'threshold' => trans('quota.threshold.not_updated_unlimited')
            ]);
        }

        $userquota->threshold = $valid_data['threshold'];

        $userquota->save();

        CalculateUserUsedQuota::dispatch($request->user());

        return redirect()->route('profile.storage.index')->with([
            'flash_message' => trans('quota.threshold.updated', ['threshold' => $userquota->threshold])
        ]);
    }
}
