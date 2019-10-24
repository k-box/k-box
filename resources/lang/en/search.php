<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Search page Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */

    'page_title' => 'Search',

    'form' => [
        'placeholder' => 'Search...',
        'placeholder_in' => 'Search ":location"...',
        'hint' => 'Search for words and phrases. You can use AND and/or OR to narrow down your search results.',
        'hint_in' => 'Search inside :location',
        'submit' => 'Start searching',
        'public_switch_alt' => 'Search for Public documents',
        'private_switch_alt' => 'Search for Private documents',
    ],

    'error' => 'There was a problem connecting to the K-Link Core for making the search. The team has been notified and is working on a solution.',

    'failure' => 'There was a problem on our end. The search request cannot be processed. Please notify the administrator if the problem persists.',

    'empty_query' => 'Insert something in the search field above and press enter to start searching.',

    'loading_filters' => 'Loading filters...',

    'no_results' => 'Sorry nothing has been found for <strong>:term</strong> into <strong>:collection</strong> documents.',
    'no_results_no_markup' => 'Sorry nothing has been found for :term in :collection',
    'no_results_generic' => 'No documents match your search criteria.',
    'no_results_for_term' => 'No documents match your search for ":term".',

    'try_message' => 'Try searching for words that start with :startwithlink',

    'facets' => [
        'language' => "Language",
        'documentType' => "Document Type",
        'documentGroups' => "Collections",
        'projectId' => 'Project',
        'collections' => "Collections",
        'projects' => 'Project',
        'copyright_usage' => 'License',
    ],

];
