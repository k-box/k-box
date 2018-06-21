<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Search page Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */

    'page_title' => 'Suche',

    'form' => [
        'placeholder' => 'Suchen...',
        'placeholder_in' => '":location" durchsuchen...',
        'hint' => 'Nach Wörtern oder Sätzen suchen, Sie können AND und/oder OR verwenden, um die Suche zu verfeinern.',
        'hint_in' => 'In :location suchen',
        'submit' => 'Suchen',
        'public_switch_alt' => 'Suche öffentliche Dokumente',
        'private_switch_alt' => 'Suche private Dokumente',
    ],

    'error' => 'Es gab ein Problem beim verbinden zur Suchmaschine. Das Team wurde benachrichtigt, und arbeitet bereits an einer Lösung.',

    'failure' => 'Es gab ein Problem auf unserer Seite, die Suchanfrage konnte nicht bearbeitet werden. Bitte informieren sie den Administrator, wenn das Problem bestehen bleibt.',

    'empty_query' => 'Etwas in das Suchfeld eingeben und Enter drücken, um mit der Suche zu beginnen.',

    'loading_filters' => 'Filter laden...',

    'no_results' => 'Keine Ergebnisse für <strong>:term</strong> in den Dokumenten für <strong>:collection</strong>.',
    'no_results_no_markup' => 'Keine Ergebnisse für :term in den Dokumenten für :collection.',
    'no_results_generic' => 'Keine Dokumente entsprachen Ihren Suchkriterien.',
    'no_results_for_term' => 'Keine Dokumente entsprachen Ihrer Suchanfrage ":term".',

    'try_message' => 'Versuchen sie nach wörtern zu suchen, die mit :startwithlink beginnen',

    'facets' => [
        'institutionId' => 'Institution',
        'language' => "Sprache",
        'documentType' => "Dokumentenart",
        'documentGroups' => "Sammlungen",
        'projectId' => 'Projekte',
        'collections' => "Sammlungen",
        'projects' => 'Projekt',
        'copyright_usage' => 'Lizenz',
    ],

];
