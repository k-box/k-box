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

    'clear_completed_btn' => 'Effacer les éléments terminés',

    'import_status_general' => '{0} Import complété|{1} :num import en cours|[2,Inf] :num imports en cours',

    'import_status_details' => ':total total, :completed complétés et :executing en cours',
    
    'preparing_import' => 'Import en préparation...',

    'form' => [
        'submit_folder' => 'Importer un dossier',
        'submit_web' => 'Importer depuis le web',

        'select_web' => 'Depuis une URL',
        'select_folder' => 'Depuis un dossier',

        'placeholder_web' => 'http(s)://monsite.com/fichier.pdf',
        'placeholder_folder' => '/chemin/vers/un/dossier',

        'help_web' => 'Veuillez insérer une URL par ligne. Les sites web nécessitant une identification ne sont pas supportés.',
        'help_folder' => 'Les disques réseaux doivent être montés sur un système de fichiers local, voir <a href=":help_page_route" target="_blank">Aide Import</a>.',

    ],
    
    /**
     * Possible import status
     */
    'status' => [
        // The import is in the queue and waits for being processed
        'queued' => 'En attente',
        // The import is put on hold
        'paused' => 'En pause',
        // The import is downloading the files
        'downloading' => 'Téléchargement en cours',
        // The import is completed
        'completed' => 'Complet',
        // The documents imported are in the search engine indexing phase
        'indexing' => 'Préparation pour la recherche en cours',
        // Import has an error
        'error' => 'Erreur',
    ],
    
    'remove' => [
        'remove_btn' => 'Supprimer',
        'remove_btn_hint' => 'Supprimer l\'import',
        'remove_dialog_title' => 'Supprimer ":import"?',
        'remove_confirmation' => 'Voulez-vous supprimer ":import"?',
        'removing' => 'Suppression en cours ":import"...',
        'removing_alt' => 'Suppression en cours...',
        'removed_message' => '":import" a été supprimé de la liste d\'importation.',
        
        // message showed when a user wants to remove an import created by another user
        'destroy_forbidden_user' => 'Vous ne pouvez pas supprimer ":import" de la liste d\'importation parce que vous n\'êtes pas le créateur de l\'import.',
        // This version is used when the import filename cannot be retrieved because the file is deleted
        'destroy_forbidden_user_alternate' => 'Vous ne pouvez pas supprimer l\'import parce que vous n\'êtes pas son créateur.',
        
        // message showed when the remove action has been requested on import with a status different than "completed" or "error"
        'destroy_forbidden_status' => 'Vous ne pouvez pas supprimer les imports qui sont en attente ou en cours de téléchargement.',
        
        // General error when something not-expected happen
        'destroy_error' => 'L\'import ne peut pas être supprimé. Si le problème persiste, veuillez transmettre ce message au service de support: ":error"',
        'destroy_error_dialog_title' => 'L\'import ne peut pas être supprimé',
    ],
    
    'retry' => [
        'retry_btn' => 'Réessayer',
        'retry_btn_hint' => 'Veuillez essayer l\'importation encore une fois',
        'retrying' => 'Nouvel essai ":import"...', // the import can only be added back to the queue of the imports
        'retrying_alt' => 'Nouvel essai en cours...',
        'retry_completed_message' => '":import" a été ajouté à la liste des imports en cours.',
        
        // message showed when a user wants to retry an import created by another user
        'retry_forbidden_user' => 'Vous ne pouvez pas réessayer l\'import de ":import" parce que vous n\'êtes pas son créateur.',
        // This version is used when the import filename cannot be retrieved because the file is deleted
        'retry_forbidden_user_alternate' => 'Vous ne pouvez pas réessayer l\'import parce que vous n\'êtes pas son créateur.',
        
        'retry_error_file_not_found' => 'Il n\'a pas été possible de réessayer l\'import parce que les données ont été effacées',
        
        'retry_forbidden_status' => 'Vous ne pouvez pas réessayer les imports qui ne sont pas bloqués par une erreur.',
        
        // General error when something not-expected happen
        'retry_error' => 'Cet import ne peut pas être réessayé. Si le problème persiste, veuillez transmettre ce message au service de support: ":error"',
        'retry_error_dialog_title' => 'Impossible de réessayer',
    ],
    

];
