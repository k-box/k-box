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

    'klink_network_name' => 'Öffentliches K-Link Netzwerk',
    'menu_public_klink' => 'K-Link öffentlich',
    'menu_public' => ':network',
    'menu_public_hint' => 'Erkunden sie die Dokumente in :network',

    'make_public' => 'Öffentlich machen',
    'publish_to_short' => 'Veröffentlichen',
    'publish_to_long' => 'In :network veröffentlichen',


    'publish_to_hint' => 'Wählen sie Dokumente aus, die auf :network veröffentlicht werden sollen',


    'publish_btn' => 'Veröffentlichen',

    'settings' => [
        'section' => 'Netzwerk beitreten',
        'section_help' => 'Hier können sie konfigurieren, wie die K-Box einem Netzwerk beitritt',
        'enabled' => 'Veröffentlichen von Dokumenten im Netzwerk aktivieren',
        'debug_enabled' => 'Netzwerkverbindung debuggen',
        'username' => 'Der Nutzer, mit dem sich im Netzwerk angemeldet wird',
        'password' => 'Das Passwort, mit dem sich im Netzwerk angemeldet wird',
        'url' => 'Die URL des Einstiegspunktes in das Netzwerk',
        'name_en' => 'Netzwerkname (englische Version)',
        'name_ru' => 'Netzwerkname (russische Version)',
        'name_section' => 'Netzwerkname',
        'name_section_help' => 'Vergeben Sie einen Namen für das Netzwerk. Es wird in der Nutzeroberfläche beim Publizieren von Dokumenten oder Sammlungen angezeigt. Sind beide Felder leer, wird "K-Link Public Network" verwendet',
        'streaming_section' => 'Video-Streamdienst',
        'streaming_section_help' => 'Setzen Sie die URL vom Video-Streamdienst, welche Verwendet wird um Videos zu publizieren',
        'streaming_service_url' => 'Die URL des Video-Streamdienstes',
    ],

    'made_public' => ':num Dokument wurde auf :network veröffentlicht|:num Dokumente wurden auf :network veröffentlicht.',

    'make_public_error' => 'Die Veröffentlichung ist fehlgeschlagen. :error',
    'make_public_error_title' => 'Konnte nicht in :network veröffentlichen',

    'make_public_success_text_alt' => 'Die Dokumente sind nun in :network verfügbar',
    'make_public_success_title' => 'Publizieren abgeschlossen',

    'making_public_title' => 'Publiziere auf :network...',
    'making_public_text' => 'Bitte warten Sie, währen die Dokumente in :network zur Verfügung gestellt werden',

    'make_public_change_title_not_available' => 'Der Titel kann nicht vor dem Publizieren verändert werden.',

    'make_public_all_collection_dialog_text' => 'Alle Dokumente in dieser Sammlung werden auf :network publiziert.',
    'make_public_inside_collection_dialog_text' => 'Sie werden alle Dokumente in ":item" auf :network veröffentlichen.',

    'make_public_dialog_title' => '":item" auf :network publizieren',
    'make_public_dialog_title_alt' => 'Auf :network publizieren',


    'make_public_empty_selection' => 'Bitte wählen sie alle Dokumente aus, die auf :network veröffentlicht werden sollen.',

    'make_public_dialog_text' => 'Sie werden ":item" auf :network veröffentlichen.',
    'make_public_dialog_text_count' => 'Sie werden :count Dokumente auf :network veröffentlichen.',

    'publication_error_copyright' => 'Hey, Sie versuchen ein Dokument zu veröffentlichen, das keinen Rechteinhaber hat. Diese Information muss vor dem Veröffentlichen angegeben werden.',
];
