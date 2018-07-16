<?php

namespace OneOffTech\LanguageGuesser;

use OneOffTech\LanguageGuesser\Drivers\LanguageCli;

/**
 * @see \OneOffTech\LanguageGuesser\LocalLanguageGuesser
 */
class LanguageGuesserFactory
{
    /**
     * Create a new video processor instance.
     *
     * @return \OneOffTech\LanguageGuesser\Contracts\LanguageGuesser
     */
    public function make()
    {
        return new LocalLanguageGuesser();
    }

    public static function isInstalled()
    {
       return LanguageCli::isInstalled();
    }
}
