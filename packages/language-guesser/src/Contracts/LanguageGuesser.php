<?php


namespace OneOffTech\LanguageGuesser\Contracts;

interface LanguageGuesser
{

    /**
     * Guess the language of a text
     *
     * @param string $text
     * @param array $blacklist
     * @return string the ISO 639-1 code
     */
    public function guess($text, $blacklist = []);
    
    // /**
    //  * Guess the language of a text
    //  *
    //  * @param string $text
    //  * @param array $blacklist
    //  * @return array the array of languages identified, the key is the language code, while the value is the probability
    //  */
    // public function guessAll($text, $blacklist = []);
}
