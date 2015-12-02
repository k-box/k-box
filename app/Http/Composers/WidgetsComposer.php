<?php namespace KlinkDMS\Http\Composers;

use Illuminate\Contracts\View\View;

use Illuminate\Contracts\Auth\Guard as AuthGuard;
use KlinkDMS\User;
use Carbon\Carbon;

class WidgetsComposer {

    /**
     * ...
     *
     * @var 
     */
    protected $adapter;

    /**
     * [$documents description]
     * @var \Klink\DmsDocuments\DocumentsService
     */
    private $documents = NULL;

    /**
     * Create a new profile composer.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct(\Klink\DmsAdapter\KlinkAdapter $adapter, \Klink\DmsDocuments\DocumentsService $documentsService)
    {
        
        $this->adapter = $adapter;

        $this->documents = $documentsService;
    }

    /**
     * Storage Widget
     *
     * @param  View  $view
     * @return void
     */
    public function widgetStorage(View $view)
    {
        if(\Auth::check()){

            $auth_user = \Auth::user();

            if($auth_user->isDMSAdmin() || $auth_user->isContentManager()){

                $storage = $this->documents->getStorageStatus();

                $view->with('storage_status', $storage);

            }
        }
    }


    /**
     * Hero counter widget
     * @param  View   $view [description]
     * @return [type]       [description]
     */
    public function widgetHeroCounter(View $view)
    {
        $public = $this->adapter->getDocumentsCount('public');
        $private = $this->adapter->getDocumentsCount('private');

        $view->with('document_total', $public+$private);
        $view->with('document_public', $public);
    }


    public function widgetRecentDocuments(View $view)
    {

        if(\Auth::check()){

            $auth_user = \Auth::user();

            if($auth_user->isDMSAdmin() || $auth_user->isContentManager()){

                $recent_documents = $this->documents->getRecentDocuments();

                $view->with('recent_documents', $recent_documents);
                
            }
            else {

                $recent_documents = $this->documents->getRecentDocuments(7, $auth_user);
                
                $view->with('recent_documents', $recent_documents);

            }

        }
        else {
            $view->with('recent_documents', []);
        }
    }

    public function widgetStarred(View $view)
    {

        if(\Auth::check()){

            $auth_user = \Auth::user();

            $stars = $auth_user->starred()->with('document')->take(5)->orderBy('created_at', 'DESC')->get();

            $view->with('starred', $stars);
        }

    }

    public function widgetRecentSearches(View $view)
    {

        if(\Auth::check()){

            $auth_user = \Auth::user();

            $recent = $auth_user->searches()->take(5)->orderBy('updated_at', 'desc')->get();

            $view->with('recent_searches', $recent);
        }
        else {
            $view->with('recent_searches', []);
        }

    }
    
    public function widgetUserSessions(View $view)
    {
        
        $active_users = array();

        if(\Auth::check()){

            $auth_user = \Auth::user();

            $sessions_table_name = app()->config['session.table'];
		
    		$sessions_driver_db = app()->config['session.driver'] === 'database';
    		
    		$table_exists = \Schema::hasTable($sessions_table_name);
            
    		if($sessions_driver_db || ($sessions_driver_db && $table_exists)){
    		
        		$sessions = \DB::table($sessions_table_name)->where('last_activity', '>=', time() - (20*60))->get();
        
        		foreach ($sessions as $session)
        		{
        			
        			$payload = @unserialize(base64_decode($session->payload));
        			
        			$login_user = array_first($payload, function($key, $value){
        				return starts_with($key, 'login_');
        			});
        			
        			try{
        			
        				if(!is_null($login_user)){
                            
                            $u = User::findOrFail($login_user);
                            
                            $active_users[] = array(
                                'time' => Carbon::createFromTimeStamp($session->last_activity)->diffForHumans(),
                                'user' => $u->name,
                                'is_me' => $u->id === $auth_user->id
                            );
        				}
        			
        			}catch(\Exception $ex){
        				
        				// $this->line( date(trans('units.date_format'), $session->last_activity) . ' ' . $session->id);
        				
        				// $this->log( ' > User not found: ' . $login_user);
        			}
                    
                    if(empty($active_users)){
                        $active_users[] = array(
                                'time' => Carbon::now()->diffForHumans(),
                                'user' => $auth_user->name,
                                'is_me' => true
                            );
                    }
        			
        		}
            
            }

        }
        
        $view->with('active_users', $active_users);
        

    }

}