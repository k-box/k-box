<?php

namespace KBox\Http\Controllers\Document;

use KBox\User;
use KBox\Shared;
use KBox\Project;
use Carbon\Carbon;
use KBox\Traits\Searchable;
use KBox\DocumentDescriptor;
use Illuminate\Http\Request;
use KBox\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard as AuthGuard;
use KBox\Sorter;

class RecentDocumentsController extends Controller
{
    use Searchable;

    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('capabilities');
    }

    public function index(AuthGuard $auth, Request $request, $range = 'currentweek')
    {
        $base_now = Carbon::now();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $init_of_month = $base_now->copy()->startOfMonth();
        $init_of_month_diff = $base_now->copy()->startOfMonth()->diffInDays($today);
        $start_of_week = $today->copy()->previous(Carbon::MONDAY);
        $last_7_days = $base_now->copy()->subDays(7);
        $last_30_days = $today->copy()->subMonth();

        $user = $auth->user();

        $user_is_dms_manager = $user->isDMSManager();

        $order = $request->input('o', 'd') === 'a' ? 'ASC' : 'DESC';

        $selected_range = $user->optionRecentRange();

        if ($selected_range !== $range) {
            $selected_range = $range;
            $user->setOption(User::OPTION_RECENT_RANGE, $range);
        }

        $req = $this->searchRequestCreate($request);

        $from = $today;
        $to = Carbon::now();

        if ($selected_range === 'yesterday') {
            $from = $yesterday;
        } elseif ($selected_range === 'currentweek') {
            $from = $last_7_days;
        } elseif ($selected_range === 'currentmonth') {
            $from = $last_30_days;
        }

        $sorter = Sorter::fromRequest($request, 'document', 'update_date', 'd');

        $documents = $this->getLastUpdatesQuery($user, $from, $to, $sorter);

        $req->visibility('private');

        $results = $this->search($req, function ($_request) use ($documents) {
            if ($_request->isPageRequested() && ! $_request->isSearchRequested()) {
                return $documents->get();
            }

            $_request->in($documents->get()->pluck('uuid')->all());

            return false; // force to execute a search on the core instead on the database
        });

        $grouped = $results->getCollection()->groupBy(function ($date) use ($start_of_week, $init_of_month, $init_of_month_diff) {
            if ($date->updated_at->isToday()) {
                $group = trans('units.today');
            } elseif ($date->updated_at->isYesterday()) {
                $group = trans('units.yesterday');
            } elseif ($date->updated_at->diffInDays($start_of_week) <= 6) {
                $group = trans('units.this_week');
            } elseif ($date->updated_at->diffInDays($init_of_month) < $init_of_month_diff-1) {
                $group = trans('units.this_month');
            } else {
                $group = trans('units.older');
            }

            return $group;
        });

        return view('documents.recent', [
            'search_terms' => $req->term,
            'is_search_requested' => $req->isSearchRequested(),
            'search_replica_parameters' => $request->only('s'),
            'pagination' => $results,
            'range' => $selected_range,
            'order' => $order,
            'info_message' => $user_is_dms_manager ? trans('documents.messages.recent_hint_dms_manager') : null,
            'list_style_current' => $user->optionListStyle(),
            'pagetitle' => trans('documents.menu.recent').' '.trans('documents.page_title'),
            'documents' => $grouped,
            'groupings' => array_keys($grouped->toArray()),
            'context' => 'recent',
            'filter' => trans('documents.menu.recent'),
            'sorting' => $sorter,
        ]);
    }

    /**
     * Get the updated documents within a specific Period
     *
     * @param KBox\User $user
     * @param Carbon\Carbon $from
     * @param Carbon\Carbon $to
     * @return \Illuminate\Database\Eloquent\Builder the query to execute to retrieve the updated documents
     */
    private function getLastUpdatesQuery(User $user, Carbon $from, Carbon $to, Sorter $sorter)
    {
        // this is used by internal queries, so we change
        // the sort only if refers to updated_at column
        $order = $sorter->column === 'updated_at' ? $sorter->order : 'DESC';

        $user_is_dms_manager = $user->isDMSManager();

        $document_ids = collect();

        // private documents directly updated
        $personal_documents = DocumentDescriptor::local()
            ->private()
            ->when(! $user_is_dms_manager, function ($query) use ($user) {
                return $query->ofUser($user->id);
            })
            ->where('updated_at', '>=', $from)
            ->where('updated_at', '<=', $to)
            ->distinct()->select('id')->take(config('dms.recent.limit'))->orderBy('updated_at', $order);
        
        $document_ids = $document_ids->merge($personal_documents->get());

        // last shared from other users
        $shared_documents = Shared::sharedWithMe($user)
            ->where('updated_at', '>=', $from)
            ->where('updated_at', '<=', $to)
            ->where('shareable_type', '=', \KBox\DocumentDescriptor::class)
            ->select('shareable_id as id')->take(config('dms.recent.limit'))->orderBy('updated_at', $order);

        $document_ids = $document_ids->merge($shared_documents->get());

        if (! $user_is_dms_manager) {
            // documents updated in a project I have access to
            $documents_in_projects = $user->projects()->orWhere('projects.user_id', $user->id)->get()->reduce(function ($carry, $prj) use ($from, $to, $order) {
                return $carry->merge($prj->documents()
                    ->where('updated_at', '>=', $from)
                    ->where('updated_at', '<=', $to)
                    ->take(config('dms.recent.limit'))
                    ->distinct()
                    ->select('id')
                    ->orderBy('updated_at', $order)->get());
            }, collect());

            $document_ids = $document_ids->merge($documents_in_projects);
        }

        // get the unique ids of the documents, so we don't select the same document more than once
        $list_of_docs = $document_ids->unique(function ($u) {
            return $u['id'];
        });

        // we use "where in" with an array because, in MySQL, union queries cannot be be used as source of the "where in select"
        return DocumentDescriptor::whereIn('id', $list_of_docs->toArray())
            ->where('updated_at', '>=', $from)
            ->where('updated_at', '<=', $to)
            ->take(config('dms.recent.limit'))
            ->orderBy($sorter->column, $sorter->order);
    }
}
