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


	'form' => array(
		'placeholder' => 'Search',
		'submit' => 'Start searching',
		'public_switch_alt' => 'Search for Public documents',
		'private_switch_alt' => 'Search for Private documents',
	),

	'error' => 'There was a problem connecting to the K-Link Core for making the search. The team has been notified and is working on a solution.',

	'empty_query' => 'Insert something in the search field above and press enter to start searching.',

	'loading_filters' => 'Loading filters...',

	'no_results' => 'Sorry nothing has been found for <strong>:term</strong> into <strong>:collection</strong> documents.',

	'try_message' => 'Try search into :collectionlink documents or for words that starts with :startwithlink',


	'facets' => array(
		'institutionId' => 'Institution',
		'language' => "Language",
        'documentType' => "Document Type",
	),

];
