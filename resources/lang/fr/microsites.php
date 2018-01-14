<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Microsites related Language Lines
    |--------------------------------------------------------------------------
    */

    'page_title' => 'Microsites de projets',
    'page_title_with_name' => 'Microsite de projet pour :project',
    
    'pages' => [
        'create' => 'Créer un microsite pour le projet ":project"',
        'edit' => 'Modifier un microsite pour le projet ":project"',
    ],
    
    
    'hints' => [
        'what' => 'Un microsite vous permet de créer une page publique pour votre projet',
        'create_for_project' => 'Créer un microsite pour le projet',
        'for_project' => 'Créer un microsite pour le projet',
        'delete_microsite' => 'Supprimer le microsite de projet',
        'edit_microsite' => 'Modifier le contenu et les paramètres du microsite',
        
        'site_title' => 'Le nom du site web qui era montré aux utilisateurs',
        'slug' => 'La version conviviale de l\'URL du site web. Cela aidera les utilisateurs à trouver et se souvenir de l\'adresse. Ne peut pas commencer avec le mot "create".',
        'logo' => 'Le logo du site web, avec une taille maximale de 280x80 pixels. L\'image peut être stockée sur n\'importe quel serveur bénéficiant d\'une connexion sécurisée HTTPS',
        'default_language' => 'La langue dans laquelle le site web sera affiché, si l\'utilisateur ne choicit pas spécifiquement une autre langue',

        'content' => 'Ici vous pouvez spécifier le contenu de la page du micro-site et, optionnellement, le menu de navigation. Pour le moment, vous pouvez spécifier le contenu uniquement en anglais et en russe.',
        
        'page_title' => 'Le tire de la page. Le titre par défaut est The "home"',
        'page_slug' => 'La version conviviale de l\'URL du site web. Cela aidera les utilisateurs à trouver et se souvenir de l\'adresse',
        'page_content' => 'Vous pouvez ajouter du texte, des liens et du texte formatté. Le formattage se base sur <a href="https://daringfireball.net/projects/markdown/basics" target="_blank">la syntaxe Markdown</a>. Vous pouvez aussi insérer des liens ou des éléments intégrés d\'autres sites web. Par exemple vous pouvez intégrer un flux RSS en mettant ce code sur une ligne: <code>@rss:https://klinktest.wordpress.com/feed/</code>. Veuillez noté que le contenu intégré sera mis en cache pour prévenir une utilisation excessive des ressources du serveur. La durée de vie du cache se situe entre 1 et 4 heures, selon le service',
    ],
    
    'actions' => [
        'create' => 'Créer un microsite',
        'edit' => 'Modifier un microsite',
        'save' => 'Enregistrer les paramètres du microsite',
        'delete' => 'Effacer le microsite',
        'delete_ask' => 'Vous allez effacer le microsite de":title". Etes-vous sûr de vouloir l\'effacer?',
        'view_site' => 'Voir le microsite',
        'publish' => 'Publier le microsite',
        'view_project_documents' => 'Aller au projet',
        'search' => 'Rechercher dans K-Link...',
        'search_project' => 'Rechercher :project...',
    ],
    
    'messages' => [
        'created' => 'Le microsite ":title" a été créé et il est accessible sous <a href=":site_url" target="_blank">:slug</a>',
        'updated' => 'Le microsite ":title" a été mis à jour',
        'deleted' => 'Le microsite ":title" a été effacé. L\'URL publique du microsite ne sera plus accessible',
    ],
    
    'errors' => [
        'create' => 'Il y a eu un problème durant la création du microsite. :error',
        'create_no_project' => 'Veuillez spécifier un projetPlease. Aucun projet n\'a été spécifié pour permettre la création du microsite.',
        'create_already_exists' => 'Un microsite existe déjà pour le projet ":project". Vous ne pouvez pas avoir plus d\'un microsite par projet.',
        'delete' => 'Il y a eu un problème durant la suppression du microsite. :error',
        'update' => 'Il y a eu un problème durant la modification du microsite. :error',
        'delete_forbidden' => 'Vous ne pouvez pas effacer le microsite ":title" car vous n\'êtes pas les gestionnaire du projet apparenté au microsite.',
        'forbidden' => 'Vous devez être administrateur du projet pour pouvoir interagir avec les microsites.',
        'user_not_affiliated_to_an_institution' => 'Vous n\'êtes affilié à aucune institution. Veuillez demander une modification de votre profil avant de créer un microsite.',
    ],
    
    'labels' => [
        'microsite' => 'Microsite<sup>beta</sup>',
        'site_title' => 'Nom du site',
        'slug' => 'Nom convivial du site',
        'site_description' => 'Description du site',
        'logo' => 'Le logo du site web. Veuillez indiquer une URL vers une image (l\'url doit être en HTTPS)',
        'default_language' => 'Langue par défaut du site web',
        'cancel_and_back' => 'Annuler et retourner au projet',
        'publishing_box' => 'Publier',
        'content' => 'Contenu du microsite',
        
        'content_en' => 'Version anglaise du contenu',
        'content_ru' => 'Version russe du contenu',
        
        'page_title' => 'Le titre de la page à créer',
        'page_slug' => 'Le nom convivial de la page',
        'page_content' => 'Le contenu de la page',
    ],
];
