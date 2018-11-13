<?php

namespace KBox\Http\Controllers;

use KBox\HomeRoute;
use Illuminate\Contracts\Auth\Guard;

/**
 * @deprecated
 */
class HomeController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Home Controller
    |--------------------------------------------------------------------------
    |
    | This controller renders your application's "dashboard" for users that
    | are authenticated. Of course, you are free to change or remove the
    | controller as you wish. It is just here to get your app started!
    |
    */

    /**
     * [$adapter description]
     * @var \Klink\DmsAdapter\KlinkAdapter
     */
    private $adapter = null;

    /**
     * [$documents description]
     * @var \KBox\Documents\Services\DocumentsService
     */
    private $documents = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(\Klink\DmsAdapter\KlinkAdapter $adapterService, \KBox\Documents\Services\DocumentsService $documentsService)
    {
        $this->middleware('auth');

        $this->adapter = $adapterService;

        $this->documents = $documentsService;
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index(Guard $auth)
    {
        if ($auth->check()) {
            $auth_user = $auth->user();

            return redirect(HomeRoute::get($auth_user));
        } else {
            return view('welcome');
        }
    }
}
