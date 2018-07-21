<?php

namespace KBox\Http\Middleware;

use App;
use Config;
use Session;
use Jenssegers\Date\Date as LocalizedDate;

/**
 * Set the language locale based on the configured language for the user and/or the browser language
 */
final class Locale
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        $language = Session::get('language', config('app.locale'));
        $force = false;

        if (auth()->check()) {
            // if the user is authenticated get the language configured in the options
            $user_selected = auth()->user()->optionLanguage();

            if (! is_null($user_selected)) {
                $language = $user_selected;
                $force = true;
            }
        }

        $browser_language_preference = $request->header('ACCEPT_LANGUAGE', null);

        if (! $force && ! empty($browser_language_preference)) {
            // set the locale of the browser if available

            $languages = collect(explode(',', $browser_language_preference));

            $keyed = $languages->map(function ($item) {
                $lang = substr(ltrim($item), 0, 2);
                if (strlen($lang) < 2) {
                    $lang = config('app.locale');
                }
                $factor = '1.0';

                if (str_contains($item, ';q=')) {
                    $factor = str_after($item, ';q=');
                }

                return compact('lang', 'factor');
            })->sortByDesc('factor')->first();

            $language = $keyed['lang'];
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
