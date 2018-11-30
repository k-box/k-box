<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Document and Document Descriptor Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for localizing the document description
    | meta information and the document administration menu and title
    |
    */

    'descriptor' => [

        'name' => 'name',
        'added_by' => 'hinzugefügt von',
        'language' => 'sprache',
        'added_on' => 'hinzugefügt am',
        'last_modified' => 'zuletzt geändert',
        'indexing_error' => 'Das Dokument wurde nicht im K-Link indiziert',
        'private' => 'Privat',
        'shared' => 'Geteilt',
        'is_public' => 'Öffentliches Dokument',
        'is_public_description' => 'Dieses Dokument ist öffentlich für andere Institutionen im K-Link Netzwerk',
        'trashed' => 'Dieses Dokument ist im Papierkorb',
        'klink_public_not_mine' => 'Dieses Dokument ist nur eine Referent zu einem Dokument im K-Link Netzwerk, daher kann es nicht bearbeitet werden.',
    ],

    'page_title' => 'Dokumente',

    'menu' => [
        'all' => 'Alle',
        'public' => 'K-Link Öffentlich',
        'private' => 'Privat',
        'personal' => 'My Uploads',
        'starred' => 'Markiert',
        'shared' => 'Mit mir geteilt',
        'recent' => 'Letzte',
        'trash' => 'Papierkorb',
        'not_indexed' => 'Nicht indiziert',
        'recent_hint' => 'Hier befinden sich all ihre zuletzt modifizierten Dokumente',
        'starred_hint' => 'Hier befinden sich all ihre markierten Dokumente',
    ],

    'sort' => [
        'sorted_by' => 'Sortiert nach :sort',
        'type_project_name' => 'Projektname',
        'type_search_relevance' => 'Relevanz',
        'type_updated_at' => 'zuletzt geändert',
    ],

    'filtering' => [
        'date_range_hint' => 'Bevorzugter Zeitbereich',
        'items_per_page_hint' => 'Nummer der Einträge pro Seite',
        'today' => 'Heute',
        'yesterday' => 'Seit Gestern',
        'currentweek' => 'Letzte Woche',
        'currentmonth' => 'Letzten Monat',
    ],

    'visibility' => [
        'public' => 'Öffentlich',
        'private' => 'Privat',
    ],

    'type' => [

        'web-page' => 'Webseite|Webseiten',
        'document' => 'Dokument|Dokumente',
        'spreadsheet' => 'Tabellenkalkulation|Tabellenkalkulationen',
        'presentation' => 'Presentation|Presentationen',
        'uri-list' => 'Linkliste|Linklisten',
        'image' => 'Bild|Bilder',
        'geodata' => 'Geodaten|Geodaten',
        'text-document' => 'Textdokument|Textdokumente',
        'video' => 'Video|Videos',
        'archive' => 'Archiv|Archive',
        'PDF' => 'PDF|PDFs',
    ],

    'empty_msg' => 'Keine Dokumente in <strong>:context</strong>',
    'empty_msg_recent' => 'Keine Dokumente für <strong>:range</strong>',

    'bulk' => [

        'removed' => ':num Datei entfernt.|:num Dateien entfernt.',

        'permanently_removed' => ':num Datei permanent gelöscht.|:num Dateien permanent gelöscht.',

        'restored' => ':num Datei wiederhergestellt.|:num Dateien wiederhergestellt.',

        'remove_error' => 'Kann Dateien nicht löschen. :error',

        'copy_error' => 'Kann nicht in die Sammlung Kopieren. :error',

        'copy_completed_all' => 'Alle Dokumente wurden zu :collection hinzugefügt',
        'copy_completed_some' => '{0}Keine Dokumente wurden hinzugefügt, da sie bereits in ":collection" waren|[1,Inf]:count Dokumente wurden zu :collection hinzugefügt, die verbleibenden :remaining waren bereits in :collection',

        'restore_error' => 'Kann Dokument nicht wiederherstellen. :error',

        // 'make_public' => ':num document has been published over the K-Link Public Network|:num documents were made available in the K-Link Network.',

        // 'make_public_error' => 'The publish operation was not completed due to an error. :error',
        // 'make_public_error_title' => 'Cannot publish in K-Link Network',

        // 'make_public_success_text_alt' => 'The documents are now publicly available on the K-Link Network',
        // 'make_public_success_title' => 'Publish completed',

        'adding_title' => 'Füge Dokumente hinzu...',
        'adding_message' => 'Bitte warten, während die Dokumente zur Sammlung hinzugefügt werden...',
        'added_to_collection' => 'Hinzugefügt',
        'some_added_to_collection' => '{0}Dokumente nicht hinzugefügt|[1,Inf]Einige Dokumente nicht hinzugefügt',

        'add_to_error' => 'Konnte nicht zur Sammlung hinzufügen',

        // 'making_public_title' => 'Publishing...',
        // 'making_public_text' => 'Please wait while the documents will be made publicly available in the K-Link Network',

        // 'make_public_change_title_not_available' => 'The option for changing title before Publish is not currently available.',

        // 'make_public_all_collection_dialog_text' => 'You will make all the documents in this collection publicly available on the K-Link Network. (click outside to undo)',
        // 'make_public_inside_collection_dialog_text' => 'You will make all the documents inside ":item" publicly available on the K-Link Network. (click outside to undo)',

        // 'make_public_dialog_title' => 'Publish ":item" on K-Link Network',
        // 'make_public_dialog_title_alt' => 'Publish on K-Link Network',

        // 'publish_btn' => 'Publish!',
        // 'make_public_empty_selection' => 'Please select the documents you want to make available in the K-Link Network.',

        // 'make_public_dialog_text' => 'You will make ":item" publicly available on the K-Link Network. (click outside to stop)',
        // 'make_public_dialog_text_count' => 'You will make :count documents publicly available on the K-Link Network. (click outside to stop)',
    ],

    'create' => [
        'page_breadcrumb' => 'Erstellen',
        'page_title' => 'Neues Dokument erstellen',
    ],

    'edit' => [
        'page_breadcrumb' => ':document bearbeiten',
        'page_title' => ':document bearbeiten',

        'title_placeholder' => 'Dokumententitel',

        'abstract_label' => 'Zusammenfassung',
        'abstract_placeholder' => 'Zusammenfassung des Dokumentes',

        'authors_label' => 'Autoren',
        'authors_help' => 'Autoren müssen als durch Kommas getrennte Liste angegeben werden, z.B <code>Vorname Nachname &lt;mail@something.com&gt;</code>',
        'authors_placeholder' => 'Autoren des Dokuments (Vorname Nachname <mail@something.com>)',

        'language_label' => 'Sprache',

        'last_edited' => 'Zuletzt bearbeitet <strong>:time</strong>',
        'created_on' => 'Erstellt <strong>:time</strong>',
        'uploaded_by' => 'Hochgeladen von <strong>:name</strong>',

        'public_visibility_description' => 'Dieses Dokument wird für alle Institutionen im Netzwerk verfügbar gemacht',


        'not_index_message' => 'Dieses Dokument wurde noch nicht erfolgreich im Netzwerk publiziert. Versuchen sie es zu <button type="submit">reindizieren</button> oder kontaktieren sie den Administrator.',
        'not_fully_uploaded' => 'Dieses Dokument wird gerade hochgeladen.',
        'preview_available_when_upload_completes' => 'Die Vorschau wird angezeigt, sobald das Dokument hochgeladen ist.',

        'license' => 'Lizenz',
        'license_help' => 'Lizenzen ermöglichen es Rechteinhabern, anderen zu erlauben ihr Werk zu verwenden.',
        'license_choose_help_button' => 'Hilfe zur Lizenzauswahl',

        'copyright_owner' => 'Rechteinhaber',
        'copyright_owner_help' => 'Informationen über den Rechteinhaber. Diese sind unabhängig von der gewählten Lizenz',

        'copyright_owner_name_label' => 'Name',
        'copyright_owner_email_label' => 'E-Mail',
        'copyright_owner_website_label' => 'Webseite',
        'copyright_owner_address_label' => 'Adresse',

    ],

    'update' => [
        'error' => 'Kann das Dokument nicht Aktualisieren. Keine Änderungen wurden angewendet. :error',

        'removed_from_title' => 'Aus der Sammlung entfernt',
        'removed_from_text' => 'Das Dokument wurde aus ":collection" entfernt',
        'removed_from_text_alt' => 'Das Dokument wurde aus der Sammlung entfernt',

        'cannot_remove_from_title' => 'Konnte nicht aus der Sammlung entfernen',
        'cannot_remove_from_general_error' => 'Konnte nicht aus der Sammlung entfernen, Wenn das Problem bestehen bleibt, bitte den Administrator kontaktieren.',

    ],

    'restore' => [

        'restore_dialog_title' => ':document wiederherstellen?',
        'restore_dialog_text' => 'Sie sind dabei ":document" wiederherzustellen',
        'restore_dialog_title_count' => ':count Dokumente wiederherstellen?',
        'restore_dialog_text' => 'Sie sind dabei ":document" wiederherzustellen',
        'restore_dialog_text_count' => 'Sie sind dabei :count Dateien wiederherzustellen',
        'restore_dialog_yes_btn' => 'Ja, wiederherstellen',
        'restore_dialog_no_btn' => 'Nein',

        'restore_success_title' => 'Wiederhergestellt',
        'restore_error_title' => 'Konnte nicht wiederherstellen',
        'restore_error_text_generic' => 'Die gewählte Datei wurde nicht aus dem Papierkorb verschoben.',

        'restoring' => 'Wird Wiederhergestellt...',
    ],

    'delete' => [

        'dialog_title' => '":document" in den Papierkorb verschieben?',
        'dialog_title_alt' => 'Dokument in den Papierkorb verschieben?',
        'dialog_title_count' => ':count Dokumente in den Papierkorb verschieben?',
        'dialog_text' => 'Sie sind dabei :document in den Papierkorb zu verschieben.',
        'dialog_text_count' => 'Sie sind dabei :count Dokumente in den Papierkorb zu verschieben',
        'deleted_dialog_title' => ':document wurde in den Papierkorb verschoben',
        'deleted_dialog_title_alt' => 'In den Papierkorb verschoben',
        'cannot_delete_dialog_title' => 'Konnte ":document" nicht in den Papierkorb verschieben',
        'cannot_delete_dialog_title_alt' => 'Konnte nicht in den Papierkorb verschieben',
        'cannot_delete_general_error' => 'Es gab ein Problem beim verschieben in den Papierkorb, bitte den Administrator benachrichtigen.',
    ],

    'permanent_delete' => [

        'dialog_title' => '":document" permanent löschen?',
        'dialog_title_alt' => 'Dokument permanent löschen?',
        'dialog_title_count' => ':count Dokumente löschen?',
        'dialog_text' => 'Sie sind dabei :document permanent zu löschen. Dies kann nicht wieder rückgängig gemacht werden.',
        'dialog_text_count' => 'Sie sind dabei :count Dokumente permanent zu löschen. Dies kann nicht wieder rückgängig gemacht werden.',
        'deleted_dialog_title' => ':document wurde gelöscht',
        'deleted_dialog_title_alt' => 'Permanent gelöscht',
        'cannot_delete_dialog_title' => 'Konnte ":document" nicht permanent löschen!',
        'cannot_delete_dialog_title_alt' => 'Konnte nicht permanent löschen!',
        'cannot_delete_general_error' => 'Es gab ein Problem beim permanenten löschen des Dokuments, bitte den Administrator benachrichtigen.',
    ],

    'preview' => [
        'page_title' => 'Vorschau von :document',
        'error' => 'Es war nicht möglich die Vorschau von ":document" zu laden.',
        'not_available' => 'Die Vorschau kann für dieses Dokument nicht gezeigt werden.',
        'google_file_disclaimer' => ':document ist eine Datei auf Google Drive, wir können die Vorschau hier nicht anzeigen. Sie müssen sie in Google Drive betrachten.',
        'google_file_disclaimer_alt' => 'Für die Google Drive Datei konnte keine Vorschau generiert werden.',
        'open_in_google_drive_btn' => 'In Google Drive öffnen',
        'video_not_ready' => 'Das Video wird bearbeited, und wird in kürze verfügbar sein.',
    ],

    'versions' => [

        'section_title' => 'Versionen',

        'section_title_with_count' => 'Eine Version|:number Versionen',

        'version_count_label' => ':number Version|:number Versionen',

        'version_number' => 'Version Nummer :number',

        'version_current' => 'aktuell',

        'new_version_button' => 'Neue Version hochladen',

        'new_version_button_uploading' => 'Hochladen...',

        'filealreadyexists' => 'Die Datei die Sie hochladen existiert bereits auf der K-Box',
    ],

    'messages' => [
        'updated' => 'Dokumentendetails geändert. Verarbeiten der Änderung, möglicherweise ist das Dokument kurz nicht über die Suche zu finden.',
        'processing' => 'Das Dokument wird von der K-Box verarbeitet, und ist möglicherweise kurz nicht über die Suche zu finden.',
        'local_public_only' => 'Im moment werden nur die Öffentlichen Dokumente der Institution angezeigt.',
        'forbidden' => 'Sie haben nicht die Berechtigung zum bearbeiten von Dokumenten.',
        'delete_forbidden' => 'Sie haben nicht die Berechtigung zum löschen von Dokumenten, bitte wenden Sie sich an einen Projektmanager oder Administrator.',
        'delete_public_forbidden' => 'Sie können öffentliche Dokumente nicht löschen, bitte wenden Sie sich an einen K-Linker oder Administrator.',
        'delete_force_forbidden' => 'Sie dürfen Dokumente nicht permanent löschen. Bitte wenden Sie sich an einen Projektmanager oder Administrator.',
        'drag_hint' => 'Datei hineinziehen, um mit dem Upload zu beginnen.',
        'recent_hint_dms_manager' => 'Sie betrachten alle Änderungen an Dokumenten, die von den Nutzern der K-Box gemacht wurden.',
        'no_documents' => 'Keine Dokumente. Sie können neue Dokumente hochladen, indem Sie den "Erstellen oder hinzufügen" Knopf oben verwenden oder die Dokumente durch "drag and drop" hier hineinziehen.',
    ],


    'trash' => [

        'clean_title' => 'Papierkorb leeren?',
        'yes_btn' => 'Ja, leeren',
        'no_btn' => 'Nein',

        'empty_trash' => 'Nichts im Papierkorb',

        'empty_all_text' => 'Alle Dokumente im Papierkorb werden permanent gelöscht. Dies wird Dateien und Revisionen, Markierungen, Sammlungen und geteilte Dokumente entfernen. Dies kann nicht wieder rückgängig gemacht werden.',
        'empty_selected_text' => 'Ausgewählte Dokumente im Papierkorb werden permanent gelöscht. Dies wird Dateien und Revisionen, Markierungen, Sammlungen und geteilte Dokumente entfernen. Dies kann nicht wieder rückgängig gemacht werden.',

        'cleaned' => 'Papierkorb geleert',
        'cannot_clean' => 'Konnte Papierkorb nicht leeren',
        'cannot_clean_general_error' => 'Es gab ein Problem beim leeren des Papierkorbes, bitte einen Administrator banachrichtigen, falls das Problem bestehen bleibt.',
    ],


    'upload' => [
        'folders_dragdrop_not_supported' => 'Ihr Browser unterstürzt das Hineinziehen von Ordnern nicht.',
        'error_dialog_title' => 'Fehler beim Hochladen',

        'max_uploads_reached_title' => 'Entschuldigung, aber Sie müssen sich etwas gedulden',
        'max_uploads_reached_text' => 'Wir können nur eine bestimmte Menge an Dateien verarbeiten, also warten sie bitte etwas vor dem Hinzufügen neuer Dateien.',

        'all_uploaded' => 'Alle Dateien erfolgreich hochgeladen.',

        'upload_dialog_title' => 'Hochladen',
        'page_title' => 'Hochladen',
        'dragdrop_not_supported' => 'Ihr Browser unterstützt das Hineinziehen von Ordnern nicht.',
        'dragdrop_not_supported_text' => 'Bitte laden sie Ihre Dateien mit dem "Hinzufügen"-Dialog hoch.',
        'remove_btn' => "Datei entfernen", //this is the little link that is showed after the file upload has been processed
        'cancel_btn' => 'Hochladen abbrechen', //for future use
        'cancel_question' => 'Sind Sie sich sicher, das Sie das Hochladen abbrechen wollen?',  //for future use
        'outside_project_target_area' => 'Bitte ziehen sie Ihre Datei über ein Projekt, um sie hochzuladen.',
        'empty_file_error' => 'Leere Datei. Bitte laden Sie eine Datei mit zumindest einem Wort hoch.',
    ],
];
