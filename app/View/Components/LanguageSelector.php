<?php

namespace KBox\View\Components;

use Illuminate\View\Component;

class LanguageSelector extends Component
{
    public const TYPE_FORM = 'form';

    public const TYPE_DROPDOWN = 'dropdown';

    /**
     * Current language selected.
     * Represented by the language code
     *
     * @var string
     */
    public $current;

    /**
     * Currently supported languages
     *
     * @var array
     */
    public $languages;

    /**
     * The type of the selector to show
     *
     * @var string available options 'form' or 'dropdown'
     */
    public $type;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($current = null, $type = 'form')
    {
        $this->current = $current ?? app()->getLocale();

        $this->type = $type;

        $this->languages = [
            // sort is based on the alphabetical order
            // of the ISO 639-1 language code, as seen in Wikipedia
            "de",
            "en",
            "fr",
            "ky",
            "ru",
            "tg",
        ];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.language-selector');
    }

    public function isDropdown()
    {
        return $this->type == self::TYPE_DROPDOWN;
    }
}
