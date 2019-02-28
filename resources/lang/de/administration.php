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

        'accounts'=>'Nutzerkonten',
        'language'=>'Sprache',
        'storage'=>'Speicher',
        'network'=>'Netzwerk',
        'mail'=>'E-Mail',
        'update'=>'Aktualisierung und Wiederherstellung',
        'maintenance'=>'Wartung und Ereignisse',
        'institutions'=>'Institutionen',
        'settings'=>'Einstellungen',
        'identity' => 'Identitäten',
        'licenses' => 'Dokumentenlizenzen',

    ],

    'accounts' => [

        'disable_confirm' => 'Soll :name wirklich gesperrt werden?',

        'create_user_btn' => 'Nutzer anlegen',

        'table' => [

            'name_column' => 'Name',
            'email_column' => 'E-Mail',
            'institution_column' => 'Institution',

        ],

        'edit_account_title' => ':name bearbeiten',

        'labels' => [

            'email' => 'E-Mail',
            'username' => 'Nutzername',
            'perms' => 'Berechtigungen',

            'cancel' => 'Abbrechen',

            'create' => 'Erstellen',
            'update' => 'Aktualisieren',

            'institution' => 'Institution',
            'select_institution' => 'Auswahl der Institution des Nutzers...',

        ],

        'capabilities' => [

            'manage_dms' => 'Nutzer kann den administrativen Bereich der K-Box verwenden',
            'change_document_visibility' => 'Nutzer kann Dokumente (de-)publizieren',
            'edit_document' => 'Nutzer kann Dokumente bearbeiten',
            'delete_document' => 'Nutzer kann Dokumente löschen',
            'upload_documents' => 'Nutzer kann Dokumente hochladen',
            'make_search' => 'Nutzer kann auf alle unpublizierten Dokumente in zugänglichen Projekten zugreifen',
            'manage_own_groups' => 'Nutzer kann persönliche Sammlungen hinzufügen/entfernen',
            'manage_institution_groups' => 'Nutzer kann Dokumentsammlungen in zugänglichen Projekten hinzufügen/entfernen',
            'manage_project_collections' => 'Nutzer kann Projektsammlungen in zugänglichen Projekten hinzufügen/entfernen',
            'manage_share' => 'Nutzer kann Projektdokumente direkt mit anderen K-Boxes teilen',
            'receive_share' => 'Nutzer kann Dokumente sehen, die mit ihm geteilt wurden',
            'manage_share_personal' => 'Nutzer kann persönliche Dokumente direkt mit anderen K-Boxes teilen',
            'manage_share_private' => 'Nutzer kann Dokumente mit Nutzergruppen seiner Institution teilen',
            'clean_trash' => 'Nutzer kann Dokumente auf der K-Box permanent löschen',
            'manage_personal_people' => 'Nutzer kann Nutzergruppen erstellen/bearbeiten',
            'manage_people' => 'Nutzer kann institutionelle Nutzergruppen erstellen/bearbeiten',

        ],

        'types' => [

            'guest' => 'Gast',
            'partner' => 'Partner',
            'content_manager' => 'Content Manager',
            'quality_content_manager' => 'Quality Content Manager',
            'project_admin' => 'Projektadministrator',
            'admin' => 'K-Box Admin',
            'klinker' => 'K-Linker',

        ],

        'create' => [

            'title' => 'Neues Nutzerkonto erstellen',
            'slug' => 'Erstellen',

        ],

        'created_msg' => 'Nutzer erstellt, das Password wurde an die E-Mailadresse des Benutzers versandt',
        'edit_disabled_msg' => 'Die Berechtigungen des eigenen Nutzerkontos können nicht verändert werden. Das Profil kann auf der <a href=":profile_url">Profilseite</a> geändert werden.',
        'disabled_msg' => 'Nutzer :name gesperrt',
        'enabled_msg' => 'Nutzer :name wurde entsperrt',
        'updated_msg' => 'User aktualisiert',
        'mail_subject' => 'Ihr K-Box Nutzerkonto ist bereit',
        'reset_sent' => 'Passwort E-Mail versandt an :name (:email)',
        'reset_not_sent' => 'Die Passwort E-Mail konnte nicht an :email versendet werden. :error',
        'reset_not_sent_invalid_user' => 'Nutzer :email konnte nicht gefunden werden.',
        'send_reset_password_btn' => 'Passwort zurücksetzen',
        'send_reset_password_hint' => 'Einen Passwort-Link an den Nutzer senden',
        'send_message_btn' => 'Nachricht senden',
        'send_message_btn_hint' => 'Sendet eine Nachricht an jeden Nutzer',
    ],

    'language' => [

        'list_label' => 'Liste der unterstützten Sprachen.',
        'code_column' => 'Sprachcode',
        'name_column' => 'Sprache',

    ],

    'storage' => [

        'disk_status_title' => 'Festplattenstatus',
        'documents_report_title' => 'Dokumentenarten',
        'disk_number' => 'Festplatte :number',
        'disk_type_all' => 'Haupt- und Dokumentenfestplatte',
        'disk_type_main' => 'Hauptfestplatte',
        'disk_type_docs' => 'Dokumentenfestplatte',
        'disk_space' => ':free <strong>frei</strong>, :used verwendet von insgesamt :total.',

        'reindexall_btn' => 'Alle dokumente neu indizieren',

        'reindexing_status' => 'Indiziere :number Dokumente...',
        'reindexing_all_status' => 'Indiziere alle Dokumente...',
        'reindexing_status_completed' => 'Alle Dokumente wurden neu indiziert.',

        'naming_policy_title' => 'Namensschema',
        'naming_policy_description' => 'Sie können den Upload von Dokumenten verweigern, die nicht diesem Namenschema folgen',

        'naming_policy_btn_activate' => 'Aktivieren',
        'naming_policy_btn_save' => 'Aktualisieren',
        'naming_policy_btn_deactivate' => 'Deaktivieren',

        'naming_policy_msg_activated' => 'Namensschema aktiviert',
        'naming_policy_msg_deactivated' => 'Namensschema deaktiviert',

    ],

    'network' => [

        'klink_net_title' => 'K-Link Netzwerkverbindung',
        'ksearch' => 'K-Search Suchmaschinenverbindung',
        'ksearch_description' => 'Zeigt den Zustand der Verbindung zwischen K-Box und der Suchmaschine.',

        'network' => 'Connection to ":network"',
        'network_description' => 'Zeigt den Zustand der Verbindung zwischen K-Box und dem Netzwerk.',


        'klink_status' => [
            'success' => 'Verbindung hergestellt',
            'failed' => 'Verbindungsfehler',
        ]

    ],
    'mail' => [
        'save_btn' => 'E-Mail Einstellungen speichern',
        'configuration_saved_msg' => 'Die E-Mail Einstellungen wurden erfolgreich gespeichert.',
        'test_success_msg' => 'Die Testmail wurde gesendet (von :from). Überprüfen sie ihren Posteingang.',
        'test_failure_msg' => 'Die Testmail konnte aufgrund eines Fehlers nicht gesendet werden.',
        'enable_chk' => 'Versenden von E-Mails aktivieren',
        'enabled' => 'Die K-Box kann emails versenden',
        'enabled_by_configuration' => 'Das versenden von E-Mails wurde über die Konfiguration aktiviert',
        'disabled' => 'Die K-Box kann keine E-Mails versenden',
        'test_btn' => 'Testmail versenden',
        'from_label' => 'E-Mail versenden von',
        'from_description' => 'Absender für alle von der K-Box versendeten E-Mails',
        'server_configuration_label' => 'Servereinstellungen',
        'server_configuration_description' => 'Wie sich die K-Box mit dem Mailserver verbindet',
        'from_name' => 'Name (z.B. John)',
        'from_address' => 'E-Mail Adresse (z.B. john@klink.org)',
        'from_name_placeholder' => 'John',
        'from_address_placeholder' => 'z.B. john@klink.asia',
        'host_label' => 'SMTP Host Address',
        'port_label' => 'SMTP Host Port',
        'encryption_label' => 'Der E-Mail Server muss TLS Verschlüsselung unterstützen',
        'username_label' => 'SMTP Server Nutzername',
        'password_label' => 'SMTP Server Passwort',
        'log_driver_used' => 'Der \'log\' Modus ist aktiviert. Die Servereinstellungen können nicht geändert werden.',
        'log_driver_go_to_log' => 'Die E-Mails werden in das K-Box Protokoll geschrieben. Es kann unter <a href=":link">Administration > Wartung und Ereignisse</a> betrachtet werden.',
    ],
    'update' => [],
    'maintenance' => [

        'queue_runner' => 'Asynchronous process jobs runner',

        'queue_runner_started' => 'Gestartet',
        'queue_runner_stopped' => 'Beendet',

        'queue_runner_not_running_description' => 'Der runner ist nicht gestartet, also funktioneren E-Mail Nachrichten und Dokumentenindizierung möglicherweise nicht.',

        'logs_widget_title' => 'Letzte Logeinträge',
    ],


    'institutions' => [

        'edit_title' => 'Details von :name bearbeiten',
        'create_title' => 'Neue Institution erstellen',
        'create_institutions_btn' => 'Neue Institution erstellen',
        'saved' => 'Institution :name aktualisiert.',
        'update_error' => 'Institutionsdetails nicht gespeichert: :error',
        'create_error' => 'Die Institution kann nicht erstellt werden: :error',
        'delete_not_possible' => 'Die institution :name wird im Moment für Dokumente oder Nutzerberechtigungen verwendet. Bitte vor dem löschen all diese Einträge entfernen.',
        'delete_error' => 'Die Institution :name kann nicht gelöscht werden: :error',
        'deleted' => 'Die Institution :name wurde entfernt.',
        'delete_confirm' => 'Institution :name aus dem Netzwerk entfernen?',
        'deprecated' => 'Die Institutionsverwaltung wird sich in kommenden Versionen ändern. Um einen Umstieg zu erleichtern, wurde das Erstellen, Bearbeiten und Löschen von Institutionen deaktiviert.',

        'labels' => [
            'klink_id' => 'Institution Identifier (im K-Link Netzwerk)',
            'name' => 'Institutionsname',
            'email' => 'Institutions E-Mail für mehr Informationen',
            'phone' => 'Telefonnummer des Ansprechpartners der Institution',
            'url' => 'Adresse der Website der Institution',
            'thumbnail_url' => 'Institutionsbild (URL eines Bildes)',
            'address_street' => 'Straße',
            'address_country' => 'Land',
            'address_locality' => 'Stadt',
            'address_zip' => 'Postleitzahl',
            'update' => 'Details speichern',
            'create' => 'Institution erstellen'
        ],
    ],

    'settings' => [
        'viewing_section' => 'Betrachten',
        'viewing_section_help' => 'Hier kann konfiguriert werden, wie Nutzer die Dokumente betrachten.',
        'save_btn' => 'Einstellungen Speichern',
        'saved' => 'Einstellungen wurden aktualisiert. Nach einem neu-laden der Seite können Nutzer die Änderungen sehen.',
        'save_error' => 'Die Einstellungen konnten nicht gespeichert werden. :error',

        'map_visualization_chk' => 'Kartenvisualisierungen aktivieren',

        'support_section' => 'Support',
        'support_section_help' => 'Wenn diese K-Box einen Supportvertrag hat, bitte den Code einfügen. Dies erlaubt es den Nutzern Support und Unterstützung von den K-Link Entwicklern anzufragen.',
        'support_token_field' => 'Support Code',
        'support_save_btn' => 'Supporteinstellungen speichern',

        'analytics_section' => 'Nutzungsstatistiken',
        'analytics_section_help' => 'Nutzungsstatistiken erlauben einen Einblick in das Nutzerverhalten und die Nutzerzahlen. In diesem Bereich können Statistiken aktiviert werden.',
        'analytics_token_field' => 'Analytics Code',
        'analytics_save_btn' => 'Einstellungen speichern',

    ],

    'identity' => [
        'page_title' => 'Identität',
        'description' => 'Organisationsinformationen, mit deren Hilfe nutzer Sie über die "Kontaktseite" erreichen können.',
        'not_complete' => 'Kontaktinformationen unvollständig.',
        'suggestion_based_on_institution_hint' => 'Die Kontaktinformationen wurden anhand der Institutionsinformationen automatisch generiert. Bitte überprüfen sie die Informationen und speichern sie.',

        'contact_info_updated' => 'Kontaktinformationen gespeichert.',
        'update_error' => 'Kontaktinformationen konnten nicht gespeichert werden. :error',
    ],

    'documentlicenses' => [

        'no_licenses' => 'Keine Lizenzen verfügbar.',
        'view_license' => 'Lizenz Betrachten',
        'default_configuration_notice' => 'Die Standardeinstellung ist "Alle Rechte vorbehalten", erwägen sie zu einer permissiveren Lizenz zu wechseln, um Zusammenarbeit zu vereinfachen.',




        'default' => [
            'title' => 'Standardlizenz für neu hochgladene Dateien',
            'description' => 'Die Lizenz eines Dokuments bestimmt, wie andere es verwenden können. Die Standardlizenz betrifft alle neu hochgeladenen Dateien.',
            'label' => '',
            'save' => 'Standardlizenz ändern',
            'no_licenses_error' => 'Die verwendbaren Lizenzen in dieser K-Box sind noch nicht eingerichtet. Bitte konfigurieren sie sie, bevor sie eine Standardlizenz wählen.',
            'saved' => 'Standardeinstellungen gespeichert. Neue dateien werden automatisch auf ":title" gesetzt und können Individuell auf eine andere Einstellung geändert werden.',
            'select' => 'Lizenz auswählen',
            'apply_default_license_to_previous' => 'Ein Dokument ohne Lizenz aktualisieren, um die Standardlizenz anzuwenden|:count Dokumente ohne Lizenz aktualisieren, um die Standardlizenz anzuwenden',
            'apply_default_license_all' => 'Die Lizenz von allen Dokumenten auf die Standardlizenz ändern',
        ],
        'available' => [
            'title' => 'Verfügbare Lizenzen auf dieser K-Box',
            'description' => 'Mit der liste der verfügbaren Lizenzen kann eingestellt werden, welche Lizenzen von den Nutzern für Dokumente vergeben werden können',
            'label' => '',
            'save' => 'Lizenzliste speichern',
            'no_licenses_error' => 'Keine verfügbaren Lizenzen auf dieser K-Box, bitte Einstellungen überprüfen.',
            'saved' => 'Liste der verfügbaren Lizenzen aktualisiert.',
        ],
    ],

];
