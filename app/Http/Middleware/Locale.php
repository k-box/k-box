<?php

namespace KBox\Http\Middleware;

use App;
use Config;
use Session;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
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
        $language = $this->ensureLocaleSupported(
            $this->getUserLocale() ?? $this->getRequestLocale($request),
            Session::get('language', config('app.locale'))
        );

        App::setLocale($language);

        LocalizedDate::setLocale($language);

        return $next($request);
    }

    /**
     * Get the user configured locale, if authenticated
     *
     * @return string|null
     */
    private function getUserLocale()
    {
        if (! auth()->check()) {
            return null;
        }

        $user_selected = auth()->user()->optionLanguage();

        return $user_selected ?? null;
    }

    /**
     * Retrieve the browser requested locale, if available
     *
     * @return string|null
     */
    private function getRequestLocale(Request $request)
    {
        $browser_language_preference = $request->header('ACCEPT_LANGUAGE', null);

        if (empty($browser_language_preference)) {
            return null;
        }

        $languages = collect(explode(',', $browser_language_preference));

        $keyed = $languages->map(function ($item) {
            $lang = substr(ltrim($item), 0, 2);
            if (strlen($lang) < 2) {
                $lang = config('app.locale');
            }
            $factor = '1.0';

            if (Str::contains($item, ';q=')) {
                $factor = Str::after($item, ';q=');
            }

            return compact('lang', 'factor');
        })->sortByDesc('factor')->first();

        return $keyed['lang'] ?? null;
    }

    /**
     * Ensure that the selected locale is
     * supported by the application
     *
     * @param string $locale
     * @return string the locale, if supported, otherwise the default fallback locale
     */
    private function ensureLocaleSupported($locale, $default = null)
    {
        if (! $locale) {
            return $default ?? config('app.locale');
        }

        if (! Lang::hasForLocale('validation.accepted', $locale)) {
            // we check a known key that should be present
            // if the language is supported (or partially supported)
            // if the key is not present, the language
            // is not supported and the fallback will be used
            return $default ?? config('app.locale');
        }

        return $locale;
    }
}
