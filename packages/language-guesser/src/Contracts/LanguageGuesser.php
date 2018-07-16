<?php


namespace OneOffTech\LanguageGuesser\Contracts;

interface LanguageGuesser
{

    /**
     * Guess the language of a text
     *
     * @param string $text
     * @return string the ISO 639-1 code
     */
    public function guess($text);
}
