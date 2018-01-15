<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shared page Language Lines
    |--------------------------------------------------------------------------
    |
    */

    'page_title' => 'Partages',
    
    'share_btn' => 'Partager',

    'share_panel_title' => 'Partager :num élément|Partager :num éléments',
    
    'share_panel_title_alt' => 'Partager ":what"|Partager ":what" et :count autres',

    'share_created_msg' => ':num partage créé|:num partages créés',

    'with_label' => 'Partager avec',

    'what_label' => 'Voici ce que vous partagez',

    'empty_with_me_message' => 'Personne n\'a rien partagé avec vous :(',

    'empty_by_me_message' => 'Vous n\'avez partagé aucun document ou collection.',

    'shared_by_me_title' => 'Partagé par moi',
    'shared_by_me_count' => ':num élément partagé|:num éléments partagés',

    'shared_with_me_title' => 'Partagé par d\'autres',
    
    'shared_with_label' => 'Partagé par vous avec',
    'shared_by_label' => 'Partagé par',
    
    'bulk_destroy' => 'Partages effacés|Certains partages ne peuvent pas être effacés<br/>:errors',
    'removed' => 'Accès supprimé',
    'remove_error' => 'L\'accès ne peut pas être supprimé. :error',
    'unshare' => 'Supprimer le partage',
    'unsharing' => 'Suppression du partage en cours...',
    'remove' => 'Supprimer',
    'removing' => 'Suppression en cours...',
    
    'share_link_section' => 'Partager un lien',
    'download_link_copy' => 'Copier le lien de téléchargement vers le presse-papier',
    'document_link_copy' => 'Copier le lien',
    'preview_link_copy' => 'Copier le lien d\'aperçu vers le presse-papier',
    'document_link_copy_multiple' => 'Copier les liens',
    'send_link' => 'Envoyer le lien',
    'send_link_multiple' => 'Envoyer les liens',
    
    'link_copied_to_clipboard' => 'Le lien a été copié dans votre presse-papier, vous pouvez utiliser CTRL+V pour le coller quelque part.',

    'shared_on' => 'partagé le',
    
    'dialog' => [
        'title' => 'Partager',
        'subtitle_single' => ':what', // only one element to share
        'subtitle_multiple' => ':what et :count autre|:what et :count autres', // X and 1 other|X and 2 others
        'share_created' => 'Partage créé',
        'collection_shared' => 'Collection partagée',
        'collection_shared_text' => 'La collection a été partagée',
        'document_shared' => 'Document partagé',
        'document_shared_text' => 'Le document a été partagé',
        'multiple_selection_not_supported' => 'La sélection multiple n\'est pas encore supportée, nous y travaillons.',
        'publish_multiple_selection_not_supported' => 'Non disponible pour des sélections multiples.',
        'publish_collection_not_supported' => 'La publication d\'une collection n\'est pas encore supportée, nous y travaillons. En attendant, vous pouvez utiliser le bouton "Publier" en haut de la page.',

        'section_access_title' => 'Qui a accès',
        'section_linkshare_title' => 'Partage de lien',
        'section_linkshare_title_alternate' => 'Lien à partager',
        'section_publish_title' => 'Publier',

        'linkshare_hint' => 'Uniquement les utilisateurs enregistrés qui ont déjà accès à ce document peuvent l\'ouvrir.',
        'linkshare_multiple_selection_hint' => 'Uniquement les utilisateurs enregistrés qui ont déjà accès à ce document peuvent l\'ouvrir. Pour créer un lien public, veuillez sélectionner un seul document',
        'linkshare_members_only' => 'Uniquement les utilisateurs enregistrés listés ci-dessous peuvent accéder',
        'linkshare_public' => 'Toute personne disposant du lien peut accéder. Identification non nécessaire.',

        'published' => 'Publié sur :network',
        'not_published' => 'Non publié sur :network',
        'publishing' => 'Document en cours de publication...',
        'publishing_failed' => 'La publication a échoué.',
        'unpublishing' => 'Dépublication du document en cours...',
        'publish_collection' => 'Tous les documents dans la collection seront affectés.',
        'publish_already_in_progress' => 'Une publication est déjà en cours.',

        'document_is_shared' => 'Le document est accessible par',
        'collection_is_shared' => 'La collection est accessible par',
        'users_already_has_access' => ':num utilisateur|:num utilisateurs',
        'users_already_has_access_alternate' => '{0} Uniquement vous|{1} :num utilisateur|[2,Inf]:num utilisateurs',
        'users_already_has_access_with_public_link' => '{0} Uniquement vous et ceux qui ont accès au lien public|{1} Uniquement vous et ceux qui ont accès au lien public|[2,Inf]:num utilisateurs et ceux qui ont accès au lien public',
        'document_already_accessible_by_all_users' => 'Le document est déjà accessible par tous les utilisateurs de la K-Box.',
        'collection_already_accessible_by_all_users' => 'La collection est déjà accessible par tous les utilisateurs de la K-Box.',

        'add_users' => 'Ajouter des utilisateurs',
        'select_users' => 'Entrez le nom d\'utilisateur...',

        'access_by_direct_share' => 'Accès direct',
        'access_by_project_membership' => 'Projet ":project"',
        'access_by_project_membership_hint' => 'Vous avez accès au document parce que vous êtes membre de ":project"',
    ],
    'publiclinks' => [
        'public_link' => 'Lien public',
        'already_exist' => 'Un lien public pour :name existe déjà.',
        'delete_forbidden_not_your' => 'Vous ne pouvez pas effacer un lien que vous n\'avez pas créé.',
        'edit_forbidden_not_your' => 'Vous ne pouvez pas modifier un lien que vous n\'avez pas créé.',
    ],
];
