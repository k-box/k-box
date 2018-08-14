<?php

namespace KBox\Http\Controllers\Administration;

use KBox\Option;
use KBox\Jobs\ReindexAll;
use Illuminate\Http\Request;
use KBox\DocumentDescriptor;
use KBox\Documents\Services\StorageService;
use KBox\Http\Controllers\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Controller
 */
class StorageAdministrationController extends Controller
{
    use DispatchesJobs;

    /*
    |--------------------------------------------------------------------------
    | Storage Management Page Controller
    |--------------------------------------------------------------------------
    |
    | This controller respond to actions for the "storage administration page".
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('capabilities');
    }

    /**
     * Serve the administration/storage page
     *
     * @return \Illuminate\View\View
     */
    public function getIndex(StorageService $storage)
    {
        $data = [
        'used' => $storage->used(),
        'total' => $storage->total(),
        'percentage' => $storage->usedPercentage(),
        'graph' => $storage->usageGraph()
        ];

        $reindex = $this->getReindexExecutionStatus();

        if (isset($reindex['executing']) && ($reindex['executing'] == 'false' || ! $reindex['executing'])) {
            $reindex = null;
        }

        return view('administration.storage', [
        'pagetitle' => trans('administration.menu.storage'),
        'storage' => $data,
        'reindex' => $reindex,
        'is_naming_policy_active' => Option::option('dms.namingpolicy.active', false)
        ]);
    }

    private function getReindexExecutionStatus()
    {
        $items = Option::sectionAsArray('dms.reindex');

        if (isset($items['dms']['reindex'])) {
            $items = $items['dms']['reindex'];
        }

        $total = isset($items['total']) && ! empty($items['total']) ? (int)$items['total'] : 0;
        $completed = isset($items['completed']) && ! empty($items['completed']) ? (int)$items['completed'] : 0;

        $defaults = [
        'status' => trans('administration.storage.reindexing_status', ['number' => (isset($items['total'])) ? $items['total'] : 0]),
        'pending' => 0,
        'completed' => 0,
        'total' => 0,
        'progress_percentage' => $total > 0 ? round(($completed/$total)*100) : 0,
        ];

        if ($defaults['progress_percentage'] == 100) {
            $items['status'] = trans('administration.storage.reindexing_status_completed');
        }

        return array_merge($defaults, $items);
    }

    /**
     * Get the reindex all procedure status
     * @return Response
     */
    public function getReindexAll()
    {
        $reindex = $this->getReindexExecutionStatus();

        return response()->json($reindex);
    }

    /**
     * Start the Reindex All procedure
     * @return Response
     */
    public function postReindexAll(Request $request)
    {
        $all_id = DocumentDescriptor::all(['id'])->map(function ($el) {
            return $el->id;
        });

        $count = $all_id->count();

        Option::put('dms.reindex.executing', true);
        Option::put('dms.reindex.pending', $count);
        Option::put('dms.reindex.completed', 0);
        Option::put('dms.reindex.total', $count);
        Option::remove('dms.reindex.error');

        $this->dispatch(
            new ReindexAll($request->user(), $all_id->toArray())
        );

        return response()->json([
        'status' => trans('administration.storage.reindexing_status', ['number' => $count]),
        'pending' => $count,
        'completed' => 0,
        'total' => $count,
        'progress_percentage' => 0,
        ]);
    }

    /**
     * Save the configuration of the naming policy option
     */
    public function postNaming(Request $request)
    {
        if ($request->has('activate')) {
            $activate = ! ! $request->input('activate', null);

            if (! is_null($activate) && $activate) {
                Option::put('dms.namingpolicy.active', true);

                return redirect()->route('administration.storage.index')->with([
                'flash_message' => trans('administration.storage.naming_policy_msg_activated')
                ]);
            } elseif (! is_null($activate) && ! $activate) {
                Option::put('dms.namingpolicy.active', false);

                return redirect()->route('administration.storage.index')->with([
                'flash_message' => trans('administration.storage.naming_policy_msg_deactivated')
                ]);
            }
        }
    
        return redirect()->route('administration.storage.index');
    }
}
