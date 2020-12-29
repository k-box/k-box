<?php

namespace Tests\Feature\Components;

use Tests\TestCase;

class LanguageSelectorTest extends TestCase
{
    public function test_component_renders_form()
    {
        $view = $this->blade(
            '<x-language-selector />'
        );

        $sorting_fields = [
            trans("languages.de", [], 'de'),
            trans("languages.en", [], 'en'),
            trans("languages.fr", [], 'fr'),
            trans("languages.ky", [], 'ky'),
            trans("languages.ru", [], 'ru'),
            trans("languages.tg", [], 'tg'),
            trans('profile.change_language_btn'),
        ];

        $view->assertSeeTextInOrder($sorting_fields);
    }

    public function test_component_renders_dropdown()
    {
        $view = $this->blade(
            '<x-language-selector type="dropdown" />'
        );

        $sorting_fields = [
            trans("languages.de", [], 'de'),
            trans("languages.en", [], 'en'),
            trans("languages.fr", [], 'fr'),
            trans("languages.ky", [], 'ky'),
            trans("languages.ru", [], 'ru'),
            trans("languages.tg", [], 'tg'),
            trans('profile.change_language_btn'),
        ];

        $view->assertSeeTextInOrder($sorting_fields);
    }
}
