<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Administration Language Lines 
    |--------------------------------------------------------------------------
    |
    | The following language lines are used inside the DMS Administration area
    |
    */

    'page_title' => 'Administration',

    'menu' => [

        'accounts'=>'Comptes',
        'language'=>'Langue',
        'storage'=>'Stockage',
        'network'=>'Réseau',
        'mail'=>'Email',
        'update'=>'Mise à jour et récupération',
        'maintenance'=>'Maintenance et événements',
        'institutions'=>'Institutions',
        'settings'=>'Paramètres',
        'identity' => 'Identité',
        'licenses' => 'Licenses',

    ],

    'accounts' => [

        'disable_confirm' => 'Voulez-vous vraiment désactiver :name?',

        'create_user_btn' => 'Créer un utilisateur',

        'table' => [

            'name_column' => 'nom',
            'email_column' => 'email',
            'institution_column' => 'institution',

        ],
        
        'edit_account_title' => 'Modifier :name',

        'labels' => [

            'email' => 'Email',
            'username' => 'Nom d\'utilisateur',
            'perms' => 'Permissions',

            'cancel' => 'Annuler',

            'create' => 'Créer',
            'update' => 'Mettre à jour',

            'institution' => 'Institution',
            'select_institution' => 'Choisissez l\'institution de cet utilisateur...',

        ],

        'capabilities' => [

            'manage_dms' => 'L\'utilisateur peut accéder à la console d\'administration de K-Box',
            'manage_dms_users' => 'L\'utilisateur peut créer/désactiver des utilisateurs K-Box',
            'manage_dms_log' => 'L\'utilisateur peut voir les journaux K-Box',
            'manage_dms_backup' => 'L\'utilisateur peut  effectuer des sauvegardes et restauration de K-Box',
            'change_document_visibility' => 'L\'utilisateur peut publier et dépublier des documents',
            'edit_document' => 'L\'utilisateur peut modifier des documents',
            'delete_document' => 'L\'utilisateur peut mettre des documents à la corbeille',
            'import_documents' => 'L\'utilisateur peut importer des documents depuis des dossiers ou URLs distantes',
            'upload_documents' => 'L\'utilisateur peut mettre en ligne des documents',
            'make_search' => 'L\'utilisateur peut  accéder à tous les documents non publiés se trouvant dans des projets accessibles',
            'manage_own_groups' => 'L\'utilisateur peut ajouter/supprimer des collections de documents personnels',
            'manage_institution_groups' => 'L\'utilisateur peut ajouter/supprimer des collections de documents se trouvant dans des projets accessibles',
            'manage_project_collections' => 'L\'utilisateur peut ajouter/supprimer des collections de projet se trouvant dans des projets accessibles',
            'manage_share' => 'L\'utilisateur peut directement partager des documents de projet avec d\'autres utilisateurs K-Box',
            'receive_share' => 'L\'utilisateur peut voir les documents qui ont été partagés avec lui',
            'manage_share_personal' => 'L\'utilisateur peut directement partager des documents personnels avec d\'autres utilisateurs K-Box',
            'manage_share_private' => 'L\'utilisateur peut partager des documents avec des groupes d\'utilisateurs définis au niveau de l\'institution',
            'clean_trash' => 'L\'utilisateur peut supprimer ses propres documents de manière définitive',
            'manage_personal_people' => 'L\'utilisateur peut créer/éditer des groupes d\'utilisateurs définis au niveau personnel',
            'manage_people' => 'L\'utilisateur peut créer/éditer des groupes d\'utilisateurs définis au niveau de l\'institution',

        ],
        
        'types' => [

            'guest' => 'Invité',
            'partner' => 'Partenaire',
            'content_manager' => 'Gestionnaire de contenu',
            'quality_content_manager' => 'Gestionnaire de qualité de contenu',
            'project_admin' => 'Administrateur de projet',
            'admin' => 'Administrateur K-Box',
            'klinker' => 'K-Linker',

        ],

        'create' => [

            'title' => 'Créer un nouveau compte',
            'slug' => 'Créer',

        ],

        'created_msg' => 'Utilisateur créé, son mot de passe lui a été envoyé directement par email',
        'edit_disabled_msg' => 'Vous ne pouvez pas modifier les paramètres de votre compte. Le paramétrage de votre profil peut se faire sur la <a href=":profile_url">page de profil</a>.',
        'disabled_msg' => 'Utilisateur :name désactivé',
        'enabled_msg' => 'Utilisateur :name a été restauré',
        'updated_msg' => 'Utilisateur mis à jour',
        'mail_subject' => 'Votre compte K-Box est prêt',
        'reset_sent' => 'L\'email de réinitialisation du mot de passe a été envoyé à :name (:email)',
        'reset_not_sent' => 'L\'email de réinitialisation du mot de passe ne peut être envoyé à :email. :error',
        'reset_not_sent_invalid_user' => 'L\'utilisateur, :email, ne peut être trouvé.',
        'send_reset_password_btn' => 'Réinitialiser le mot de passe ',
        'send_reset_password_hint' => 'Demander un lien de réinitialisation du mot de passe pour l\'utilisateur',
        'send_message_btn' => 'Envoyer un message',
        'send_message_btn_hint' => 'Envoyer un message à chaque utilisateur',
    ],

    'language' => [

        'list_label' => 'Voici la liste des langues supportées',
        'code_column' => 'Code de la langue',
        'name_column' => 'Nom de la langue',

    ],

    'storage' => [

        'disk_status_title' => 'Etat du disque',
        'documents_report_title' => 'Types de documents',
        'disk_number' => 'Disque :number',
        'disk_type_all' => 'Disque principal et disque de documents',
        'disk_type_main' => 'Disque principal',
        'disk_type_docs' => 'Disque de documents',
        'disk_space' => ':free <strong>libres</strong>, :used utilisés de :total total.',

        'reindexall_btn' => 'Réindexer tous les documents',

        'reindexing_status' => 'Réindexation en cours: :number documents...',
        'reindexing_all_status' => 'Réindexation de tous les documents en cours...',
        'reindexing_status_completed' => 'Tous les documents ont été réindexés.',

        'naming_policy_title' => 'Convention de nommage de fichiers',
        'naming_policy_description' => 'Vous pouvez éviter la mise en ligne de fichiers qui ne respectent pas cette convention de nommage',

        'naming_policy_btn_activate' => 'Activer',
        'naming_policy_btn_save' => 'Mettre à jour',
        'naming_policy_btn_deactivate' => 'Désactiver',

        'naming_policy_msg_activated' => 'Convention de nommage activée',
        'naming_policy_msg_deactivated' => 'Convention de nommage désactivée',

    ],

    'network' => [

        'klink_net_title' => 'Connexion réseau K-Link',
        'ksearch' => 'Connexion K-Search engine',
        'ksearch_description' => 'Montrer l\'état de la connexion entre la K-Box et le moteur de recherche.',

        'network' => 'Connexion à ":network"',
        'network_description' => 'Montrer l\'état de la connexion entre la K-Box et le réseau.',


        'klink_status' => [
            'success' => 'Etabli et vérifié',
            'failed' => 'Connexion impossible',
        ]

    ],
    'mail' => [
        'save_btn' => 'Enregistrer la configuration email',
        'configuration_saved_msg' => 'L\'email de configuration a été enregistré avec succès.',
        'test_success_msg' => 'L\'email de test a été préparé pour envoi (de :from) avec succès. Alez voir votre boîte de réception.',
        'test_failure_msg' => 'L\'email de test ne peut pas être envoyé à cause d\'une erreur.',
        'enable_chk' => 'Activer l\'envoi d\'emails',
        'enabled' => 'La K-Box peut envoyer des emails',
        'enabled_by_configuration' => 'L\'envoi d\'emails est activé par le paramétrage du déploiement',
        'disabled' => 'La K-Box ne peut pas envoyer d\'emails',
        'test_btn' => 'Envoyer un email de test',
        'from_label' => 'Envoyer un email de',
        'from_description' => 'Ici, vous pouvez indiquer un nom et une adresse qui sont utilisés globalement pour tous les emails qui sont envoyés par la K-Box.',
        'server_configuration_label' => 'Configuration de serveur',
        'server_configuration_description' => 'Comment la K-Box se connecte au server email',
        'from_name' => 'Nom (ex. John)',
        'from_address' => 'Adresse email (ex. john@klink.org)',
        'from_name_placeholder' => 'John',
        'from_address_placeholder' => 'ex. john@klink.asia',
        'host_label' => 'Adresse hôte SMTP',
        'port_label' => 'Port hôte SMTP',
        'encryption_label' => 'Le serveur email doit supporter le cryptage TLS',
        'username_label' => 'Nom d\'utilisateur serveur SMTP',
        'password_label' => 'Mot de passe serveur SMTP',
        'log_driver_used' => 'Le log driver est utilisé. Vous ne pouvez pas changer la configuration du serveur.',
        'log_driver_go_to_log' => 'Les emails seront écrits dans le fichier journal K-Box. Vous pouvez le voir sur la page <a href=":link">Administration > Maintenance et événements</a>.',
    ],
    'update' => [],
    'maintenance' => [

        'queue_runner' => 'Asynchronous process jobs runner',

        'queue_runner_started' => 'Commencé et actif',
        'queue_runner_stopped' => 'Arrêté',

        'queue_runner_not_running_description' => 'Le jobs runner ne fonctionne pas et l\'indexation de messages email et de documents peut ne pas fonctionner comme attendu.',
        
        'logs_widget_title' => 'Dernières entrées du journal',
    ],
    
    
    'institutions' => [
        
        'edit_title' => 'Modifier les détails de :name',
        'create_title' => 'Créer une nouvelle institution',
        'create_institutions_btn' => 'Ajouter une nouvelle institution',
        'saved' => 'Institution :name mise à jour.',
        'update_error' => 'Détail de l\'institution non sauvegardé: :error',
        'create_error' => 'L\'institution ne peut pas être créée: :error',
        'delete_not_possible' => 'L\'institution :name est actuellement utilisée pour des documents et/ou pour l\'affiliation d\'utilisateurs. Veuillez supprimer les documents et l\'affiliation des utilisateurs avant de l\'effacer.',
        'delete_error' => 'L\'institution :name ne peut pas être supprimée: :error',
        'deleted' => 'L\'institution :name a été supprimée.',
        'delete_confirm' => 'Supprimer l\'institution :name du réseau?',
        'deprecated' => 'La gestion des institutions va changer. Afin de préparer votre K-Box pour ces changements, nous avons désactivé l\'addition, l\'édition et la suppression d\'institutions.',
        
        'labels' => [
            'klink_id' => 'Identificateur d\'institution (dans le réseau K-Link)',
            'name' => 'Nom de l\'institution',
            'email' => 'Email de l\'institution pour recevoir des informations',
            'phone' => 'Numéro de téléphone du secrétaire de l\'institution',
            'url' => 'Site web de l\'institution',
            'thumbnail_url' => 'Photo ou avatar de l\'institution (url d\'une image)',
            'address_street' => 'Adresse de l\'institution (rue)',
            'address_country' => 'Adresse de l\'institution (pays)',
            'address_locality' => 'Adresse de l\'institution (ville)',
            'address_zip' => 'Code postal',
            'update' => 'Enregistrer',
            'create' => 'Créer une institution'
        ],
    ],
    
    'settings' => [
        'viewing_section' => 'Visualisation',
        'viewing_section_help' => 'Vous pouvez paramétrer comment les utilisateurs voient les documents.',
        'save_btn' => 'Enregistrer les paramètres',
        'saved' => 'Les paramètres ont été mis à jour. Quand les utilisateurs rafraîchiront la page, ils verront les changements.',
        'save_error' => 'Les paramètres ne peuvent pas être enregistrés. :error',
        
        'map_visualization_chk' => 'Activer la vue cartographique',
        
        'support_section' => 'Support',
        'support_section_help' => 'Si vous avez un jeton pour le support, indiquez le ici pour permettre à vos utilisateurs d\'envoyer des demandes de support et pour recevoir de l\'aide de l\'équipe de développeurs K-Link.',
        'support_token_field' => 'Jeton de support',
        'support_save_btn' => 'Enregistrer les paramètres de support',

        'analytics_section' => 'Analytique',
        'analytics_section_help' => 'Analytique vous donne la possibilité de comprendre comment vos utilisateurs utilisent le système et combien d\'entre eux l\'utilisent régulièrement. Dans cette section vous pouvez gérer l\'analytique K-Link.',
        'analytics_token_field' => 'Jeton Analytique',
        'analytics_save_btn' => 'Enregistrer les paramètres Analytique',
        
    ],

    'identity' => [
        'page_title' => 'Identité',
        'description' => 'Informations relatives à votre organisation, afin que les utilisateurs puissent vous contacter depuis la page de contact.',
        'not_complete' => 'Les informations de contact ne sont pas complètes.',
        'suggestion_based_on_institution_hint' => 'Nous avons automatiquement rempli les informations de contact à partir des informations de contact de l\'institution. Veuillez les contrôler et appuyer sur Enregistrer.',

        'contact_info_updated' => 'Détails du contact enregistrés.',
        'update_error' => 'Les détails du contact n\'ont pas été mis à jour. :error',
    ],

];
