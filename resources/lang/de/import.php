<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Document Import page Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used on the Import documents page
    |
    */

    'page_title' => 'Import',

    'clear_completed_btn' => 'Clear completed',

    'import_status_general' => '{0} Import completed|{1} :num import in progress|[2,Inf] :num imports in progress',

    'import_status_details' => ':total insgesamt, :completed vollständig und :executing in Arbeit',

    'preparing_import' => 'Importieren vorbereiten...',

    'form' => [
        'submit_folder' => 'Ordner Importieren',
        'submit_web' => 'Aus dem Web importieren',

        'select_web' => 'Aus URL',
        'select_folder' => 'Aus Ordner',

        'placeholder_web' => 'http(s)://somesite.com/file.pdf',
        'placeholder_folder' => '/pfad/zu/einem/ordner',

        'help_web' => 'Bitte eine URL pro Zeile eingeben. Addressen die ein Passwort benötigen werden nicht unterstützt.',
        'help_folder' => 'Netzwerkfreigaben müssen auf der K-Box eingehängt sein, siehe <a href=":help_page_route" target="_blank">Import Hilfe</a>.',

    ],

    /**
     * Possible import status
     */
    'status' => [
        // The import is in the queue and waits for being processed
        'queued' => 'Eingereiht',
        // The import is put on hold
        'paused' => 'Pausiert',
        // The import is downloading the files
        'downloading' => 'Herunterladen',
        // The import is completed
        'completed' => 'Fertig',
        // The documents imported are in the search engine indexing phase
        'indexing' => 'Vorbereiten für die Suche',
        // Import has an error
        'error' => 'Fehler',
    ],

    'remove' => [
        'remove_btn' => 'Entfernen',
        'remove_btn_hint' => 'Entfernt den Import',
        'remove_dialog_title' => '":import" entfernen?',
        'remove_confirmation' => 'Möchten sie ":import" entfernen?',
        'removing' => 'Entferne ":import"...',
        'removing_alt' => 'Entferne...',
        'removed_message' => '":import" wurde aus der Import-Liste entfernt.',

        // message showed when a user wants to remove an import created by another user
        'destroy_forbidden_user' => 'Sie können ":import" nicht aus der Liste entfernen, da Sie ihn nicht in Auftrag gegeben haben.',
        // This version is used when the import filename cannot be retrieved because the file is deleted
        'destroy_forbidden_user_alternate' => 'Sie können den Import nicht aus der Liste entfernen, da Sie ihn nicht in Auftrag gegeben haben.',

        // message showed when the remove action has been requested on import with a status different than "completed" or "error"
        'destroy_forbidden_status' => 'Sie können Imports die bereits herunterladen nicht entfernen.',

        // General error when something not-expected happen
        'destroy_error' => 'Der Import konnte nicht entfernt werden. Wenn das Problem besteht, senden sie bitte diese Fehlermeldung an den Support: ":error"',
        'destroy_error_dialog_title' => 'Der Import konnte nicht entfernt werden',
    ],

    'retry' => [
        'retry_btn' => 'Erneut versuchen',
        'retry_btn_hint' => 'Erneut versuchen die Datei zu importieren',
        'retrying' => 'Erneutes einreihen von ":import"...', // the import can only be added back to the queue of the imports
        'retrying_alt' => 'Versuche erneut...',
        'retry_completed_message' => '":import" wurde erneut eingereiht.',

        // message showed when a user wants to retry an import created by another user
        'retry_forbidden_user' => 'Sie können ":import" nicht erneut einreihen, da Sie ihn nicht in Auftrag gegeben haben.',
        // This version is used when the import filename cannot be retrieved because the file is deleted
        'retry_forbidden_user_alternate' => 'Sie können den Import nicht erneut einreihen, da Sie ihn nicht in Auftrag gegeben haben.',

        'retry_error_file_not_found' => 'Import konnte nicht eingereiht werden, da die Originale Datei gelöscht wurde',

        'retry_forbidden_status' => 'Sie können Imports nicht erneut einreihen, wenn sie nicht fehlgeschlagen sind.',

        // General error when something not-expected happen
        'retry_error' => 'Der Import konnte nicht erneut eingereiht werden. Wenn das Problem besteht, senden sie bitte diese Fehlermeldung an den Support: ":error"',
        'retry_error_dialog_title' => 'Erneutes einreihen fehlgeschlagen',
    ],


];
