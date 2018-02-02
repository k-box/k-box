<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Administrator Messages Language Lines 
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for rendering particular messages
    | to the user
    |
    */
    
    'mail_testing_mode_msg' => 'La configuration email est invalide. Aucun message email ne sera envoyé, ni à des nouveaux utilisateurs, ni aux utilisateurs existants. <a href=":url">Changez ça!</a>',
    'mail_not_configured' => 'La configuration email nécessite votre attention.<br/><a href=":url">Veuillez vérifier les paramètres email</a>.',
    'mail_config_msg' => 'Veuillez compléter <a href=":url">la configuration du service email</a>.',
    'account_mail_msg' => 'Veuillez <a href=":url">changer votre compte email</a> pour une address email réelle, sinon vous ne pourrez pas recevoir de messages.',

    'long_running_msg' => '<strong>Processus en cours!</strong> Il semble que cette action prend un peu plus de temps que d\'habitude. Nous en sommes désolés!',
    
    'terms_of_use' => 'Lorsque vous mettez en ligne ou partagez un document, cela signifie que vous acceptez notre <a href=":policy_link">Politique de service</a>',

    'contacts_not_configured' => 'Les informations de contact nécessitent votre attention. <a href=":url">Veuillez les vérifier</a> dans la section Identité.',
    
    'default_license_not_set' => 'La licence par défaut pour les nouveaux documents n\'est pas configurée. <a href=":url">Veuillez choisir une licence par défaut</a>.',
    'available_licenses_not_set' => 'La liste des licences utilisables n\'est pas configurée. <a href=":url">Veuillez contrôler la listes des licences disponibles</a>.',
    
    'license_configuration_error' => '<strong>Les informations relatives aux droits d\'auteurs sont absents. Veuillez contacter votre administrateur</strong>.<br/>Ceci impacte vos possibilités de recherches de documents et de publications.',
    
    // general upload blocked message for users
    'uploads_blocked' => 'Cette K-Box est actuellement en mode de lecture seule. La mise en ligne de nouveaux fichiers n\'est pas autorisée à cause d\'opérations de maintenance.',
    
    // upload blocked message for administrators
    'uploads_blocked_admin' => 'Le mode de lecture seule est actif. La mise en ligne de fichiers est bloquée. Veuillez contrôler la configuration.',    
];
