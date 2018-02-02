<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Networks Language Lines
    |--------------------------------------------------------------------------
    |
    | contains messages for localizing actions on different public networks
    |
    | original strings taken from
    | - actions.make_public
    | - actions.publish_documents
    | - documents.bulk.making_public_title
    | - documents.bulk.making_public_text
    | - documents.bulk.make_public_error
    | - documents.bulk.make_public_error_title
    | - documents.bulk.make_public_success_text_alt
    | - documents.bulk.make_public_success_title
    | - documents.bulk.make_public_change_title_not_available
    | - documents.bulk.make_public_all_collection_dialog_text
    | - documents.bulk.make_public_inside_collection_dialog_text
    | - documents.bulk.make_public_dialog_title
    | - documents.bulk.make_public_dialog_title_alt
    | - documents.bulk.publish_btn
    | - documents.bulk.make_public_empty_selection
    | - documents.bulk.make_public_dialog_text
    | - documents.bulk.make_public_dialog_text_count
    |
    |
    */

    'klink_network_name' => 'Réseau K-Link Public',
    'menu_public_klink' => 'K-Link Public',
    'menu_public' => ':network',
    'menu_public_hint' => 'Explorer les documents disponibles dans le :network',

    'make_public' => 'Rendre Public',
    'publish_to_short' => 'Publier',
    'publish_to_long' => 'Publier vers :network',

    
    'publish_to_hint' => 'Sélectionnez quelques documents à publier vers le :network',
    

    'publish_btn' => 'Publier',

    'settings' => [
        'section' => 'Rejoindre un réseau',
        'section_help' => 'Ici vous pouvez configurer comment la K-Box rejoint un réseau',
        'enabled' => 'Activer la publication de documents vers le réseau',
        'debug_enabled' => 'Activer le débogage de la connexion au réseau',
        'username' => 'Utilisateur utilisé pour l\'identification sur le réseau',
        'password' => 'Mot de passe utilisé pour l\'identification sur le réseau',
        'url' => 'L\'adresse (URL) du Network Entry Point',
        'name_en' => 'Nom du réseau (anglais)',
        'name_ru' => 'Nom du réseau (russe)',
        'name_section' => 'Nom du réseau',
        'name_section_help' => 'Donner un nom au réseau. Il sera utilisé dans l\'interface utilisateur lors de la publication de documents et collections. Si les 2 champs sont vides, le nom "K-Link Public Network" sera utilisé',
        'streaming_section' => 'Service de streaming de vidéo',
        'streaming_section_help' => 'Activer le service de streaming de vidéo pour la publication de vidéos sur le réseau',
        'streaming_service_url' => 'L\'adresse (URL) du service de streaming vidéo',
    ],

    'made_public' => ':num document a été publié sur le :network|:num documents ont été publiés sur le :network.',
        
    'make_public_error' => 'L\'opération de publication n\'a pas pu être réalisée à cause d\'une erreur. :error',
    'make_public_error_title' => 'Impossible de publier vers :network',
    
    'make_public_success_text_alt' => 'Les documents sont maintenant disponibles dans :network',
    'make_public_success_title' => 'Publication complétée',

    'making_public_title' => 'En cours de publication vers :network...',
    'making_public_text' => 'Veuillez patienter pendant que les documents sont publiés vers :network',

    'make_public_change_title_not_available' => 'Il n\'est pour le moment pas possible de changer le titre avant la publication.',

    'make_public_all_collection_dialog_text' => 'Vous allez rendre tous les documents de cette collection disponibles dans :network.',
    'make_public_inside_collection_dialog_text' => 'Vous allez rendre tous les documents à l\'intérieur de ":item" disponibles dans :network.',
    
    'make_public_dialog_title' => 'Publier ":item" vers :network',
    'make_public_dialog_title_alt' => 'Publier vers :network',
    
    
    'make_public_empty_selection' => 'Veuillez sélectionner les documents que vous voulez rendre disponibles dans :network.',
    
    'make_public_dialog_text' => 'Vous allez rendre ":item" disponible dans :network.',
    'make_public_dialog_text_count' => 'Vous allez rendre :count documents disponibles dans :network.',
    
    'publication_error_copyright' => 'Attention, vous essayez de publier un document sans indiquer ses droits d\'auteurs. Cette information est nécessaire pour pouvoir publier ce document.',
];
