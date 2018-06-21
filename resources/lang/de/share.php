<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shared page Language Lines
    |--------------------------------------------------------------------------
    |
    */

    'page_title' => 'Shares',

    'share_btn' => 'Teilen',

    'share_panel_title' => 'Teile :num Element|Teile :num Elemente',

    'share_panel_title_alt' => 'Teile ":what"|Teile ":what" und :count weitere',

    'share_created_msg' => ':num Share erstellt|:num Shares erstellt',

    'with_label' => 'Teile mit',

    'what_label' => 'Hier ist was sie Teilen',

    'empty_with_me_message' => 'Niemand hat etwas mit ihnen geteilt :(',

    'empty_by_me_message' => 'Sie haben keine Dokumente oder Sammlungen geteilt.',

    'shared_by_me_title' => 'Von mir geteilt',
    'shared_by_me_count' => ':num Element geteilt|:num Elemente geteilt',

    'shared_with_me_title' => 'Von anderen geteilt',

    'shared_with_label' => 'Von ihnen geteilt mit',
    'shared_by_label' => 'Geteilt von',

    'bulk_destroy' => 'Shares gelöscht|Einige Shares konnten nicht gelöscht werden<br/>:errors',
    'removed' => 'Zugriff entfernt',
    'remove_error' => 'Der Zugriff konnte nicht entfernt werden. :error',
    'unshare' => 'Teilen rückgängig machen',
    'unsharing' => 'Teilen wird rückgängig gemacht...',
    'remove' => 'Entfernen',
    'removing' => 'Entferne...',

    'share_link_section' => 'Share link',
    'download_link_copy' => 'Download Link in die Zwischenablage kopieren',
    'document_link_copy' => 'Link kopieren',
    'preview_link_copy' => 'Vorschaulink in die Zwischenablage kopieren',
    'document_link_copy_multiple' => 'Links kopieren',
    'send_link' => 'Link senden',
    'send_link_multiple' => 'Links senden',

    'link_copied_to_clipboard' => 'Der Link wurde in die Zwischenablage kopiert. Sie können Strg+V verwenden, um Ihn an anderer Stelle einzufügen.',

    'shared_on' => 'geteilt am',

    'dialog' => [
        'title' => 'Teilen',
        'subtitle_single' => ':what', // only one element to share
        'subtitle_multiple' => ':what und :count weiteres|:what und :count weitere', // X and 1 other|X and 2 others
        'share_created' => 'Share Erstellt',
        'collection_shared' => 'Sammlung geteilt',
        'collection_shared_text' => 'Die Sammlung wurde geteilt',
        'document_shared' => 'Dokument geteilt',
        'document_shared_text' => 'Das Dokument wurde geteilt',
        'multiple_selection_not_supported' => 'Mehrfachauswahl noch nicht möglich, wir arbeiten daran.',
        'publish_multiple_selection_not_supported' => 'Nicht verfügbar bei Mehrfachauswahl.',
        'publish_collection_not_supported' => 'Veröffentlichen einer Sammlung ist noch nicht möglich, wir arbeiten daran. Sie können derweil den "Veröffentlichen" Knopf oben auf der Seite verwenden.',

        'section_access_title' => 'Wer hat Zugriff',
        'section_linkshare_title' => 'Teilen von Links',
        'section_linkshare_title_alternate' => 'Link zum teilen',
        'section_publish_title' => 'Veröffentlichen',

        'linkshare_hint' => 'Nur Registrierte Nutzer mit Zugriff auf das Dokument können es öffnen.',
        'linkshare_multiple_selection_hint' => 'Nur Registrierte Nutzer mit Zugriff auf das Dokument können es öffnen. Zum erstellen eines öffentlichen Link, bitte ein einzelnes Dokument auswählen.',
        'linkshare_members_only' => 'Nur unten aufgeführte angemeldete Benutzer haben Zugriff.',
        'linkshare_public' => 'Jeder mit dem Link hat Zugriff. Login nicht erforderlich.',

        'published' => 'Veröffentlicht auf :network',
        'not_published' => 'Nicht veröffentlicht auf :network',
        'publishing' => 'Veröffentliche das Dokument...',
        'publishing_failed' => 'Veröffentlichen fehlgeschlagen.',
        'unpublishing' => 'Mache das Dokument privat...',
        'publish_collection' => 'Alle Dokumente in der Sammlung sind betroffen.',
        'publish_already_in_progress' => 'Es wird bereits veröffentlicht.',

        'document_is_shared' => 'Das Dokument kann betrachtet werden von',
        'collection_is_shared' => 'Die Sammlung kann betrachtet werden von',
        'users_already_has_access' => ':num Nutzer|:num Nutzern',
        'users_already_has_access_alternate' => '{0} Nur Sie selbst|{1} :num Nutzer|[2,Inf]:num Nutzern',
        'users_already_has_access_with_public_link' => '{0} Nur Sie und jeder mit öffentlichem Link|{1} Nur Sie und jeder mit öffentlichem Link|[2,Inf]:num Nutzer und jeder mit öffentlichem Link',
        'document_already_accessible_by_all_users' => 'Das Dokument ist bereits zugänglich fpr alle K-Box Nutzer.',
        'collection_already_accessible_by_all_users' => 'Die Sammlung ist bereits zugänglich für alle K-Box Nutzer.',

        'add_users' => 'Nutzer hinzufügen',
        'select_users' => 'Nutzername eingeben...',

        'access_by_direct_share' => 'Direkter Zugriff',
        'access_by_project_membership' => 'Projekt ":project"',
        'access_by_project_membership_hint' => 'Sie haben Zugriff auf das Dokument, da sie ein Mitglied von ":project" sind',
    ],
    'publiclinks' => [
        'public_link' => 'Öffentlicher Link',
        'already_exist' => 'Ein öffentlicher Link für :name existiert bereits.',
        'delete_forbidden_not_your' => 'Sie können keinen Link entfernen, den sie nicht erstellt haben.',
        'edit_forbidden_not_your' => 'Sie können keinen Link bearbeiten, den sie nicht erstellt haben.',
    ],
];
