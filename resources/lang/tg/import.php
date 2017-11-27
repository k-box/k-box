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

    'page_title' => 'Воридот',

    'clear_completed_btn' => 'Тозакуни анҷом ёфт',

    'import_status_general' => '{0} Воридот ба анҷом расид|{1} :num воридот идома дорад|[2,Inf] :num воридот идома дорад',

    'import_status_details' => ':total умумӣ, :completed ба анҷом расид ва :executing дар ҷараён аст',
    
    'preparing_import' => 'Омодасозии воридот...',

    'form' => [
        'submit_folder' => 'Папкаи воридот',
        'submit_web' => 'Воридот аз сомона',

        'select_web' => 'Аз URL',
        'select_folder' => 'Аз папка',

        'placeholder_web' => 'http(s)://somesite.com/file.pdf',
        'placeholder_folder' => '/path/to/a/folder',

        'help_web' => 'Лутфан як URL дар як сатр гузоред. Суроғаи веб, аутентификацияро талаб мекунад, пуштибонӣ намешавад.',
        'help_folder' => 'Мубодилоти шабакавӣ бояд дар файлҳои системаи дохили насб карда шаванд, нигаред<a href=":help_page_route" target="_blank">Воридот кумак</a>.',

    ],
    
    /**
     * Possible import status
     */
    'status' => [
        // The import is in the queue and waits for being processed
        'queued' => 'Навбат ',
        // The import is put on hold
        'paused' => 'Таваққуф',
        // The import is downloading the files
        'downloading' => 'Воридкуни идома дорад',
        // The import is completed
        'completed' => 'Анчом ёфт',
        // The documents imported are in the search engine indexing phase
        'indexing' => 'Барои ҷустуҷӯ омода аст',
        // Import has an error
        'error' => 'Хатогӣ',
    ],
    
    'remove' => [
        'remove_btn' => 'Хориҷ кунед',
        'remove_btn_hint' => 'Воридотро хорич мекунад',
        'remove_dialog_title' => 'Хорич мекунад ":import"?',
        'remove_confirmation' => 'Шумо мехохед хорич кунед ":import"?',
        'removing' => 'Хорич кардан ":import"...',
        'removing_alt' => 'Хорич кардан...',
        'removed_message' => '":import" аз рӯйхати воридот хориҷ карда шуд.',
        
        // message showed when a user wants to remove an import created by another user
        'destroy_forbidden_user' => 'Шумо наметавонед хориҷ кунед ":import" аз руйхати воридот, чунки шумо созандаи воридот нестед.',
        // This version is used when the import filename cannot be retrieved because the file is deleted
        'destroy_forbidden_user_alternate' => 'Шумо наметавонед воридотиро хориҷ кунед, чунки шумо созандаи он нестед.',
        
        // message showed when the remove action has been requested on import with a status different than "completed" or "error"
        'destroy_forbidden_status' => 'Шумо наметавонед воридотеро, ки дар холати воридкуни карор дорад хорич кунед.',
        
        // General error when something not-expected happen
        'destroy_error' => 'Ин воридот дур карда намешавад. Агар мушкилот боқӣ мемонанд, ин паёмро ба гурухи кумак ирсол кунед: ":error"',
        'destroy_error_dialog_title' => 'Воридот хорич карда намешавад',
    ],
    
    'retry' => [
        'retry_btn' => 'Такрор кунед',
        'retry_btn_hint' => 'Кӯшиш кунед, ки воридотро такрор кунед',
        'retrying' => 'Аз нав илова кунед ":import"...', // the import can only be added back to the queue of the imports
        'retrying_alt' => 'Такрор шуда истодааст...',
        'retry_completed_message' => '":import" Аз нав ба навбати воридкунии чори дохил шуд.',
        
        // message showed when a user wants to retry an import created by another user
        'retry_forbidden_user' => 'Шумо воридотро аз нав такрор карда наметавонед ":import" чунки шумо созандаи воридот нестед.',
        // This version is used when the import filename cannot be retrieved because the file is deleted
        'retry_forbidden_user_alternate' => 'Шумо наметавонед воридотро аз нав такрор ,зеро шумо созандаи он нестед.',
        
        'retry_error_file_not_found' => 'Дуркуни анчом наёфт, азбаски маьлумоти асли вучуд надорад',
        
        'retry_forbidden_status' => 'Шумо наметавонед воридоти воридшударо, ки аз сабаби хатогӣ баста нашудаанд, боздоред.',
        
        // General error when something not-expected happen
        'retry_error' => 'воридот аз нав такрор нахохад шуд. Агар мушкилот боқӣ мемонанд, ин паёмро ба гурухи кумак ирсол кунед: ":error"',
        'retry_error_dialog_title' => 'Такрор ин амал имконият надорад',
    ],
    

];
