<?php

namespace KBox\Http\Composers;

use Illuminate\Contracts\View\View;

use KBox\User;
use Carbon\Carbon;
use Klink\DmsDocuments\StorageService;
use Auth;

class WidgetsComposer
{

    /**
     * @var \Klink\DmsDocuments\DocumentsService
     */
    private $documents = null;

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct(\Klink\DmsDocuments\DocumentsService $documentsService)
    {
        $this->documents = $documentsService;
    }

    /**
     * Storage Widget
     *
     * Grab the data to render the storage widget UI
     *
     * @param  View  $view
     * @return void
     */
    public function widgetStorage(View $view)
    {
        if (Auth::check()) {
            $storage = app(StorageService::class);

            $data = [
                'used' => $storage->used(),
                'total' => $storage->total(),
                'percentage' => $storage->usedPercentage(),
            ];

            $view->with('storage_status', $data);
        }
    }

    public function widgetStarred(View $view)
    {
        if (\Auth::check()) {
            $auth_user = \Auth::user();

            $stars = $auth_user->starred()->with('document')->take(5)->orderBy('created_at', 'DESC')->get();

            $view->with('starred', $stars);
        }
    }

    public function widgetRecentSearches(View $view)
    {
        if (\Auth::check()) {
            $auth_user = \Auth::user();

            $recent = $auth_user->searches()->take(5)->orderBy('updated_at', 'desc')->get();

            $view->with('recent_searches', $recent);
        } else {
            $view->with('recent_searches', []);
        }
    }
    
    public function widgetUserSessions(View $view)
    {
        $active_users = [];

        if (\Auth::check()) {
            $auth_user = \Auth::user();

            $sessions_table_name = app()->config['session.table'];
        
            $sessions_driver_db = app()->config['session.driver'] === 'database';
            
            $table_exists = \Schema::hasTable($sessions_table_name);
            
            if ($sessions_driver_db || ($sessions_driver_db && $table_exists)) {
                $sessions = \DB::table($sessions_table_name)->where('last_activity', '>=', time() - (20*60))->distinct()->get()->all();
        
                foreach ($sessions as $session) {
                    try {
                        $u = User::findOrFail($session->user_id);
                        
                        if (! isset($active_users[$session->user_id])) {
                            $active_users[$session->user_id] = [
                                'time' => Carbon::createFromTimeStamp($session->last_activity)->diffForHumans(),
                                'user' => $u->name,
                                'is_me' => $u->id === $auth_user->id
                            ];
                        }
                    } catch (\Exception $ex) {
                    }
                }

                if (empty($active_users)) {
                    $active_users[$auth_user->id] = [
                            'time' => Carbon::now()->diffForHumans(),
                            'user' => $auth_user->name,
                            'is_me' => true
                        ];
                }
            }
        }
        
        $view->with('active_users', array_values($active_users));
    }
}
