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

    'unknown' => 'Unbekannter generischer Fehler in der Anfrage',

    'upload' => [
        'simple' => 'Fehler beim Hochladen :description',
        'filenamepolicy' => 'Die Datei :filename hält sich nicht an das Namensschema.',
        'filealreadyexists' => 'Die Datei :filename existiert bereits.',
    ],

    'filealreadyexists' => [
        'generic' => 'Das Dokument :name existiert bereits in der K-Box unter dem Titel <strong>":title"</strong>.',
        'incollection' => 'Das Dokument ist bereits verfügbar in der Sammlung <a href=":collection_link"><strong>":collection"</strong></a> unter dem Titel <strong>":title"</strong>',
        'incollection_by_you' => 'Sie haben dieses Dokument bereits als <strong>":title"</strong> in <a href=":collection_link"><strong>":collection"</strong></a> hochgeladen',
        'by_you' => 'Sie haben dieses Dokument bereits als <strong>":title"</strong> hochgeladen',
        'revision_of_document' => 'Das Dokument ist eine Revision von <strong>":title"</strong>, hinzugefügt von :user (:email)',
        'revision_of_your_document' => 'Das Dokument ist eine Revision von ihrem Dokument <strong>:title</strong>',
        'by_user' => 'Das Dokument wurde bereits von :user (:email) auf der K-Box hochgeladen.',
        'in_the_network' => 'Das Dokument wurde bereits in <strong>:network</strong> unter dem Titel <strong>":title"</strong> publiziert. Hinzugefügt von :institution',
    ],

    'group_already_exists_exception' => [
        'only_name' => 'Eine Sammlung ":name" existiert bereits.',
        'name_and_parent' => 'Eine Sammung ":name" existiert bereits under ":parent".',
    ],

    'generic_text' => 'Ups! Ein unerwarteter Fehler ist aufgetreten.',
    'generic_text_alt' => 'Ups! Ein unerwarteter Fehler ist aufgetreten. :error',
    'generic_title' => 'Ups!',

    'reindex_all' => 'Das Reindizieren wurde aufgrund eines Fehlers. See the logs or contact the administrator.',

    'token_mismatch_exception' => 'Die Sitzung ist abgelaufen, Bitte laden sie die Seite neu.',

    'not_found' => 'Die Ressource konnte nicht gefunden werden.',

    'document_not_found' => 'Das Dokument konnte nicht gefunden werden oder wurde gelöscht.',

    'forbidden_exception' => 'Sie haben keine berechtigung, diese Seite zu sehen.',
    'forbidden_edit_document_exception' => 'Sie haben nicht die Berechtigung, das Dokument zu bearbeiten.',
    'forbidden_see_document_exception' => 'Sie können das Dokument nicht betrachten, da es vom Nutzer als persönlich markiert wurde.',

    'kcore_connection_problem' => 'Die Verbindung zum K-Link K-Core konnte nicht aufgebaut werden.',

    'fatal' => 'Schwerwiegender Fehler :reason',

    'panels' => [
        'title' => 'Ups! Etwas unerwartetes ist passiert.',
        'prevent_edit' => 'Sie können :name nicht bearbeiten',
    ],

    'group_edit_institution' => "Sie können Gruppen auf Institutionsebene nicht bearbeiten.",
    'group_edit_project' => "Sie können Projektsammlungen nicht bearbeiten.",
    'group_edit_else' => "Sie können fremde Gruppen nicht bearbeiten.",

    '503_title' => 'K-Box Wartung',
    '503_text' => 'Die <strong>K-Box</strong> wirt momentan<br/><strong>gewartet</strong><br/><small>und wird bald wieder verfügbar sein.</small>',

    '500_title' => 'Fehler - K-Box',
    '500_text' => 'Oh je! Etwas <strong>schreckliches</strong><br/>und unerwartetes <strong>ist passiert</strong>,<br/>das tut uns sehr leid.',

    '404_title' => 'Nicht auf der K-Box gefunden',
    '404_text' => 'Sieht so aus als würde <strong>die Seite</strong><br/>die sie aufrufen möchten<br/><strong>nicht mehr existieren</strong>.',

    '401_title' => 'Sie können die Seite nicht betrachten - K-Box',
    '401_text' => 'Sieht so aus als können sie die Seite aufgrund Ihres<br/><strong>Berechtigungslevels</strong> nicht betrachten.',

    '403_title' => 'Sie haben nicht die Berechtigung diese Seite zu betrachten',
    '403_text' => 'Sieht so aus als können sie die Seite aufgrund Ihres<br/><strong>Berechtigungslevels</strong> nicht betrachten.',

    '405_title' => 'Methode nicht erlaubt',
    '405_text' => 'Ruf\' mich nicht nochmal so auf!',

    '413_title' => 'Zu große Dokumentengröße',
    '413_text' => 'Die Datei, die Sie versuchen hochzuladen, überschreitet die maximale Dateigröße.',

    'klink_exception_title' => 'K-Link Dienste Fehler',
    'klink_exception_text' => 'Es gab ein Problem, mit den K-Link Diensten zu verbinden.',

    'reindex_failed' => 'Die Suchmaschine ist möglicherweise veraltet, bitte benachrichtigen Sie das Support-Team.',

    'page_loading_title' => 'Ladefehler',
    'page_loading_text' => 'Die Seite scheint sehr langsam zu laden und einige Funktionen sind möglicherweise nicht verfügbar. Bitte Seite neu laden.',

    'dragdrop' => [
        'not_permitted_title' => '\'Drag and Drop\' nicht verfügbar',
        'not_permitted_text' => 'Sie können die Drag&Drop aktion nicht ausführen.',
        'link_not_permitted_title' => 'Ziehen von Links ist nicht verfügbar',
        'link_not_permitted_text' => 'Im moment können Sie keine Links zu Webseiten hineinziehen.',
    ],

    'support_widget_opened_for_you' => 'Wir haben das support widget für Sie geöffnet. Bitte schreiben Sie uns was passiert ist, damit wir den Fehler untersuchen können. Vielen Dank für Ihre Unterstützung.',
    'go_back_btn' => 'Verstanden, zurück zur Anwendung.',

    'preference_not_saved_title' => 'Einstellungen nicht gespeichert',
    'preference_not_saved_text' => 'Entschuldingung, wir konnten ihre Einstellungen nicht speichern. Bitte versuchen sie es später noch einmal.',

    'generic_form_error' => 'Es gab einige Fehler in der Eingabe, bitte beheben Sie sie um fortzufahren.',

    'oldbrowser' => [
        'generic' => 'Ihr Browser ist veraltet, bitte aktualisieren sie ihn für ein Besseres Erlebnis.',
        'ie8' => 'Ihr Browser (Internet Explorer 8) ist veraltet. Er hat bekannte Sicherheitsprobleme und unterstützt nicht alle Funktionen der K-Box. Für ein besseres Erlebnis, aktualisieren sie ihren Browser.',
        'nosupport' => 'Ihre Browser-Version wird von der K-Box nicht unterstützt.',

        'more_info' => 'Weitere Informationen',
        'dismiss' => 'Ausblenden',
    ],

];
