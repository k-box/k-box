<?php

namespace KBox\Http\Controllers\Document;

use KBox\User;
use KBox\Group;
use KBox\Shared;
use KBox\Project;
use Carbon\Carbon;
use KBox\Capability;
use KBox\Traits\Searchable;
use KBox\DocumentDescriptor;
use Illuminate\Http\Request;
use KBox\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard as AuthGuard;

class RecentDocumentsController extends Controller
{
    use Searchable;

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
        
        $items_per_page = (int)$user->optionItemsPerPage();

        $requested_items_per_page = (int)$request->input('n', $items_per_page);
        
        $order = $request->input('o', 'd') === 'a' ? 'ASC' : 'DESC';

        try {
            if ($items_per_page !== $requested_items_per_page) {
                $user->setOptionItemsPerPage($requested_items_per_page);
                $items_per_page = $requested_items_per_page;
            }
        } catch (\Exception $limit_ex) {
        }

        // future proof for when this option will be saved in the user profile
        $selected_range = $user->optionRecentRange();

        if ($selected_range !== $range) {
            $selected_range = $range;
            $user->setOption(User::OPTION_RECENT_RANGE, $range);
        }

        $req = $this->searchRequestCreate($request);
        
        $req->limit($items_per_page);
        
        // Last Private Documents
        
        $documents_query = DocumentDescriptor::local()->private()->take(config('dms.recent.limit'));
        
        if (! $user_is_dms_manager) {
            $documents_query = $documents_query->ofUser($user->id);
        }
        
        // last shared documents from other users
        $shared_query = Shared::sharedWithMe($user)->take(config('dms.recent.limit'));

        // documents that have been updated in a project that the user has access to
        $all_projects_with_documents_query = $user->projects()->orWhere('projects.user_id', $user->id)->with('collection.documents');

        $shared_table = with(new Shared)->getTable();
        $descriptor_table = with(new DocumentDescriptor)->getTable();
        $shared_updated_at_field = $shared_table.'.updated_at';

        // limit all queries to the maximum number of documents take( config('dms.recent.limit') )

        if ($selected_range === 'today') {
            $documents_query = $documents_query->where('updated_at', '>=', $today);

            $shared_query = $shared_query->where($shared_updated_at_field, '>=', $today);

            $all_projects_with_documents_query = $all_projects_with_documents_query->whereHas('collection.documents', function ($query) use ($today, $order) {
                $query->where('document_descriptors.updated_at', '>=', $today)
                              ->orderBy('updated_at', $order);
            });
        } elseif ($selected_range === 'yesterday') {
            $documents_query = $documents_query->where('updated_at', '>=', $yesterday);

            $shared_query = $shared_query->where($shared_updated_at_field, '>=', $yesterday);

            $all_projects_with_documents_query = $all_projects_with_documents_query->whereHas('collection.documents', function ($query) use ($yesterday, $order) {
                $query->where('document_descriptors.updated_at', '>=', $yesterday)
                              ->orderBy('updated_at', $order);
            });
        } elseif ($selected_range === 'currentweek') {
            $documents_query = $documents_query->where('updated_at', '>=', $last_7_days);

            $shared_query = $shared_query->where($shared_updated_at_field, '>=', $last_7_days);

            $all_projects_with_documents_query = $all_projects_with_documents_query->whereHas('collection.documents', function ($query) use ($last_7_days, $order) {
                $query->where('document_descriptors.updated_at', '>=', $last_7_days)
                              ->orderBy('updated_at', $order);
            });
        } elseif ($selected_range === 'currentmonth') {
            $documents_query = $documents_query->where('updated_at', '>=', $last_30_days);

            $shared_query = $shared_query->where($shared_updated_at_field, '>=', $last_30_days);
            
            $all_projects_with_documents_query = $all_projects_with_documents_query->whereHas('collection.documents', function ($query) use ($last_30_days, $order) {
                $query->where('document_descriptors.updated_at', '>=', $last_30_days)
                              ->orderBy('updated_at', $order);
            });
        }

        $documents_query = $documents_query->orderBy('updated_at', $order)->get()->map(function ($descriptor) {
            return [
                'id' => $descriptor->id,
                'uuid' => $descriptor->uuid,
                'updated_at' => $descriptor->updated_at,
            ];
        });
        
        $shared_query = $shared_query->orderBy('updated_at', $order)->
                where('shareable_type', '=', 'KBox\DocumentDescriptor')
                ->with('shareable')
                ->get()->map(function ($descriptor) {
                    return [
                            'id' => $descriptor->shareable->id,
                            'uuid' => $descriptor->shareable->uuid,
                            'updated_at' => $descriptor->updated_at,
                        ];
                });

        // let's make them together
        
        // Wrapping inside a new Illuminate\Support\Collection as $documents_query might
        // be a Illuminate\Database\Eloquent\Collection. To prevent the case that optimizations based on
        // the item being instance of Illuminate\Database\Eloquent\Model are used, when is not the case,
        // it is converted to a standard collection
        $list_of_docs = collect($documents_query)->merge($shared_query);

        if (! $user_is_dms_manager) {
            // add the projects only if is not a DMS admin, otherwise only duplicates will be added
            $all_projects_with_documents = $all_projects_with_documents_query->get()->map(function ($e) {
                return $e->collection->documents;
            })->collapse()->map(function ($e) {
                return [
                    'id' => $e->id,
                    'uuid' => $e->uuid,
                    'updated_at' => $e->updated_at,
                ];
            });
            
            $list_of_docs = $list_of_docs->merge($all_projects_with_documents);
        }
        
        // sort all the ids, remove duplicates and take the maximum amount
        $list_of_docs = $list_of_docs->unique(function ($u) {
            return $u['id'];
        });

        
        $req->visibility('private');
        
        $results = $this->search($req, function ($_request) use ($user, $list_of_docs, $order) {
            if ($_request->isPageRequested() && ! $_request->isSearchRequested()) {
                $all_query = DocumentDescriptor::whereIn('id', $list_of_docs->pluck('id')->all());
                
                return $all_query->orderBy('updated_at', $order); //ASC or DESC
            }
            
            $_request->in($list_of_docs->pluck('uuid')->all());
            
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
            'filter' => trans('documents.menu.recent')]);
    }
}
