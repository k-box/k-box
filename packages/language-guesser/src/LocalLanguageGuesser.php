<?php

namespace OneOffTech\LanguageGuesser;

use OneOffTech\LanguageGuesser\Drivers\LanguageCli;
use OneOffTech\LanguageGuesser\Contracts\LanguageGuesser as LanguageGuesserContract;

class LocalLanguageGuesser implements LanguageGuesserContract
{
    public function guess($text, $blacklist = [])
    {
        $cli = new LanguageCli($text, false, $blacklist);
        $out = $cli->run();
       
        return $out;
    }

    public function isInstalled()
    {
        return LanguageCli::isInstalled();
    }
}
