<?php

namespace KBox\Http\Middleware;

use Illuminate\Contracts\Auth\Guard;
use Closure;
use Config;
use Session;
use App;
use Jenssegers\Date\Date as LocalizedDate;

/**
 * Set the language locale based on the configured language for the user and/or the browser language
 */
final class Locale
{

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $language = Session::get('language', Config::get('app.locale'));
        $force = false;

        if ($this->auth->check()) {
            // if the user is authenticated get the language configured in the options
            $user_selected = $this->auth->user()->optionLanguage();

            if (! is_null($user_selected)) {
                $language = $user_selected;
                $force = true;
            }
        }

        if (! $force && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {

            // set the locale of the browser if available

            $browser_suggests = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

            if (! empty($browser_suggests)) {
                $languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

                $pos = strrpos($languages[0], '-');

                $language = substr($languages[0], 0, $pos);
            }
        }
        
        if (empty($language)) {
            // this because the user might not have a option language property defined or might be empty
            $language = config('app.locale');
        }

        App::setLocale($language);

        LocalizedDate::setLocale($language);

        return $next($request);
    }
}
