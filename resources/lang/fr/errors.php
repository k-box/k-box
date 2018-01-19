<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Generic Errors Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used inside for rendering error messages
    |
    */

    'unknown' => 'Erreur générique dans la requête',

    'upload' => [
        'simple' => 'Erreur lors de la mise en ligne :description',
        'filenamepolicy' => 'Le fichier :filename ne respecte pas la convention de nommage.',
        'filealreadyexists' => 'Le fichier :filename existe déjà.',
    ],

    'filealreadyexists' => [
        'generic' => 'Le document :name existe déjà dans la K-Box sous le titre <strong>":title"</strong>.',
        'incollection' => 'Le document est déjà disponible dans <a href=":collection_link"><strong>":collection"</strong></a> sous le titre <strong>":title"</strong>',
        'incollection_by_you' => 'Vous avez déjà mis en ligne le document <strong>":title"</strong> dans <a href=":collection_link"><strong>":collection"</strong></a>',
        'by_you' => 'Vous avez déjà mis en ligne ce document sous le titre <strong>":title"</strong>',
        'revision_of_document' => 'Le document que vous voulez mettre en ligne est une révision existante de <strong>":title"</strong>, ajoutée par :user (:email)',
        'revision_of_your_document' => 'Le document est une révision existante de votre document <strong>:title</strong>',
        'by_user' => 'Ce document a déjà été ajouté à la K-Box par :user (:email).',
        'in_the_network' => 'Le document est déjà disponible dans <strong>:network</strong>, sous le nom <strong>":title"</strong>. Ajouté par :institution',
    ],

    'group_already_exists_exception' => [
        'only_name' => 'Une collection appelée ":name" existe déjà.',
        'name_and_parent' => 'La collection ":name" dans ":parent" existe déjà.',
    ],
    
    'generic_text' => 'Oups! quelque chose d\'inattendu s\'est passé.',
    'generic_text_alt' => 'Oups! quelque chose d\'inattendu s\'est passé. :error',
    'generic_title' => 'Oups!',

    'reindex_all' => 'La procédure de réindexation de tous les documents ne peut aboutir à cause d\'une erreur. Veuillez consulter les logs ou contacter votre administrateur.',

    'token_mismatch_exception' => 'Il semble que votre session est échue. Veuillez rafraîchir la page, puis continuer votre travail. Merci!',

    'not_found' => 'La ressource que vous cherchez ne peut être trouvée.',
    
    'document_not_found' => 'Le document que vous cherchez ne peut être trouvé ou a été effacé.',

    'forbidden_exception' => 'Vous n\'avez pas accès à cette page.',
    'forbidden_edit_document_exception' => 'Vous ne pouvez pas modifier ce document.',
    'forbidden_see_document_exception' => 'Vous ne pouvez pas voir ce document car il s\'agit d\'un document privé d\'un autre utilisateur.',
    
    'kcore_connection_problem' => 'La connexion à K-Link Core ne peut pas être établie.',

    'fatal' => 'Erreur fatale :reason',

    'panels' => [
        'title' => 'Oups! quelque chose d\'inattendu s\'est passé.',
        'prevent_edit' => 'Vous ne pouvez pas modifier :name',
    ],

    'import' => [
        'folder_not_readable' => 'Le dossier :folder n\'est pas lisible. Veuillez contrôler que vous avez les droits de lecture.',
        'url_already_exists' => 'Un fichier possédant la même adress url (:url) a déjà été importé.',
        'download_error' => 'Le document ":url" ne peut pas être téléchargé. :error',
    ],

    'group_edit_institution' => "Vous ne pouvez pas modifier des groupes au niveau de l\'institution.",
    'group_edit_project' => "Vous ne pouvez pas modifier des collections de projet.",
    'group_edit_else' => "Vous ne pouvez pas modifier les groupes de quelqu\'un d\'autre.",

    '503_title' => 'Maintenance K-Box',
    '503_text' => 'La <strong>K-Box</strong> est actuellement en <br/><strong>maintenance</strong><br/><small>bientôt à nouveau disponible :)</small>',

    '500_title' => 'Erreur - K-Box',
    '500_text' => 'Oh! Quelque chose de <strong>mauvais</strong><br/> et inattendu <strong>s\'est passé</strong>,<br/>nous sommes désolés.',

    '404_title' => 'Pas trouvé sur la K-Box',
    '404_text' => 'Il semble que <strong>la page</strong><br/>que vous cherchez<br/><strong>n\'existe plus</strong>.',
    
    '401_title' => 'Vous ne pouvez pas voir cette page - K-Box',
    '401_text' => 'Il semble que vous <strong>ne pouvez pas</strong> voir la page<br/>à cause de votre <strong>niveau d\'autorisation</strong>.',
    
    '403_title' => 'Vous n\'avez pas la permission de voir cette page',
    '403_text' => 'Il semble que vous <strong>ne pouvez pas</strong> voir la page<br/>à cause de votre <strong>>niveau d\'autorisation</strong>.',

    '405_title' => 'Méthode interdite sur la K-Box',
    '405_text' => 'Ne m\'appelez plus jamais comme ça.',
    
    '413_title' => 'Taille de document excessive',
    '413_text' => 'Le fichier que vous essayez de mettre en ligne dépasse la taille maximale autorisée.',
    
    'klink_exception_title' => 'Erreur des services K-Link',
    'klink_exception_text' => 'Il y a eu un problème pendant la connexion aux services K-Link.',
    
    'reindex_failed' => 'La recherche peut ne pas être à jour avec les changements les plus récents. Veuillez contacter l\'équipe de support pour de plus amples informations.',
    
    'page_loading_title' => 'Problème de chargement',
    'page_loading_text' => 'Le chargement de la page semble être lent et certaines fonctionalités peuvent ne pas être disponibles. Veuillez rafraîchir la page.',
    
    'dragdrop' => [
        'not_permitted_title' => 'Glisser-déposer non disponible',
        'not_permitted_text' => 'Vous ne pouvez pas effectuer de glisser-déposer.',
        'link_not_permitted_title' => 'Le glisser-déposer de liens n\'est pas disponible',
        'link_not_permitted_text' => 'Vous ne pouvez actuellement pas glisser-déposer des liens vers des sites web.',
    ],

    'support_widget_opened_for_you' => 'Nous avons ouvert pour vous le widget de support. Veuillez nous envoyer un message pour que nous puissions examiner l\'erreur. Merci de votre support.',
    'go_back_btn' => 'J\'ai compris! Faites moi sortir d\'ici.',
    
    'preference_not_saved_title' => 'Préférences non enregistrées',
    'preference_not_saved_text' => 'Désolé, nous n\'avons pas pu enregistrer vos préférences. Veuillez essayer à nouveau plus tard.',

    'generic_form_error' => 'Vous avez quelques erreurs, veuillez les corriger avant de continuer',

    'oldbrowser' => [
        'generic' => 'Votre navigateur est périmé. Pour une meilleure expérience et pour plus de sécurité, veuillez garder votre navigateur à jour.',
        'ie8' => 'Votre navigateur (Internet Explorer 8) est périmé. Il a des failles de sécurité et ne peut pas afficher toutes les fonctionnalités de K-Link. Pour une meilleure expérience, veuillez garder votre navigateur à jour.',
        'nosupport' => 'La version de votre navigateur n´est pas supportée par la K-Box.',
        
        'more_info' => 'Plus d\'information',
        'dismiss' => 'Rejeter',
    ],

];
