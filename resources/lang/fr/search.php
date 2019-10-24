<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Search page Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */

    'page_title' => 'Recherche',

    'form' => [
        'placeholder' => 'Rechercher...',
        'placeholder_in' => 'Rechercher ":location"...',
        'hint' => 'Rechercher des mots et des phrases. Vous pouvez utiliser les mots clés AND et/ou OR pour affiner vos recherches.',
        'hint_in' => 'Chercher à l\'intérieur de :location',
        'submit' => 'Lancer la recherche',
        'public_switch_alt' => 'Rechercher des documents publics',
        'private_switch_alt' => 'Rechercher des documents privés',
    ],

    'error' => 'Il y a eu un problème lors de la connexion à K-Link Core pour effectuer une recherche. L\'équipe technique en a été informée et recherche une solution.',

    'failure' => 'Il y a eu un problème de notre côté. La recherche ne peut pas être traitée. Veuillez contacter l\'administrateur si le problème se reproduit.',

    'empty_query' => 'Ecrivez quelque chose dans le champ de recherche ci-dessus et appuyez sur Enter pour lancer la recherche.',

    'loading_filters' => 'Chargement des filtres...',

    'no_results' => 'Désolé, aucun résultat n\'a été trouvé pour <strong>:term</strong> parmi les documents de <strong>:collection</strong>.',
    'no_results_no_markup' => 'Désolé, rien n\'a été trouvé pour :term dans :collection',
    'no_results_generic' => 'Aucun document ne correspond à vos critères de recherche.',
    'no_results_for_term' => 'Aucun document ne correspond à votre recherche pour ":term".',

    'try_message' => 'Veuillez essayer de chercher des mots commençant avec :startwithlink',

    'facets' => [
        'language' => "Langue",
        'documentType' => "Type de Document",
        'documentGroups' => "Collections",
        'projectId' => 'Projet',
        'collections' => "Collections",
        'projects' => 'Project'
    ],

];
