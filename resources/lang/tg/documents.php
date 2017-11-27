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

        'name' => 'Ном',
        'added_by' => 'Илова кард',
        'language' => 'Забон',
        'added_on' => 'Илова шуд дар таърихи',
        'last_modified' => 'Тағйироти охирин',
        'indexing_error' => 'Санад дар K-Link индексатсия карда нашуд',
        'private' => 'Хусусӣ',
        'shared' => 'Муштарак',
        'is_public' => 'Санади умуми',
        'is_public_description' => 'Ин санад ба таври умуми ба Ташкилотхои дигар дар K-Link дастрас аст',
        'trashed' => 'Ин санад дар кутии партов чойгир аст',
        'klink_public_not_mine' => 'Санади мазкур танҳо истинод ба санади ба K-Link иловашуда мебошад, бинобар ин, шумо ягон тағйиротро анҷом дода наметавонед.',
    ],

    'page_title' => 'Санадхо',

    'menu' => [
        'all' => 'Ҳама',
        'public' => 'K-Link Public',
        'private' => 'Хусусӣ',
        'personal' => 'Шахсӣ',
        'starred' => 'Ситорачадор',
        'shared' => 'Муштарак',
        'recent' => 'Oхирон',
        'trash' => 'Кутии партов',
        'not_indexed' => 'Индексатсия карда нашудааст',
        'recent_hint' => 'Шумо дар ин ҷо  санадхои ба наздикӣ тағйир шударо пайдо хоҳад кард',
        'starred_hint' => 'Шумо дар ин ҷо  санадхои интихоб кардаи (ситорачадор) пайдо хоҳад кард',
    ],

    'sort' => [
        'sorted_by' => 'Аз руйи :sort мураттабшуда',
        'type_project_name' => 'Номи лоиҳа',
        'type_search_relevance' => 'Ҷустуҷӯи мувофикӣ ',
        'type_updated_at' => 'Санаи навсозӣ',
    ],

    'filtering' => [
        'date_range_hint' => 'Итихоби вақти мувофик',
        'items_per_page_hint' => 'Шумораи санадхо дар саҳифа',
        'today' => 'Имрӯз',
        'yesterday' => 'Аз Дирўз',
        'currentweek' => '7 рузи охир',
        'currentmonth' => '30 рузи охир',
    ],

    'visibility' => [
        'public' => 'Умуми',
        'private' => 'Хусусӣ',
    ],

    'type' => [

        'web-page' => 'веб саҳифа|веб саҳифаҳо',
        'document' => 'cанад|санадҳо',
        'spreadsheet' => 'ҷадвали электронӣ|ҷадвалҳои электронӣ',
        'presentation' => 'презентация|презентацияхо',
        'uri-list' => 'Рӯйхати URL|Рӯйхатхои URL',
        'image' => 'Сурат|суратхо',
        'geodata' => 'Маълумоти ҷуғрофӣ| маълумотҳои ҷуғрофӣ',
        'text-document' => 'Санади матнӣ|санадҳои матнӣ',
        'video' => 'Видео|Видеоҳо',
        'archive' => 'Бойгони|Бойгониҳо',
        'PDF' => 'PDF|PDFs',
    ],

    'empty_msg' => 'Санад вучуд надорад <strong>:context</strong>',
    'empty_msg_recent' => 'Санад вучуд надоард барои <strong>:range</strong>',

    'bulk' => [

        'removed' => ':num унсур дур шуд.|:num унсур дур шуданд.',
        
        'permanently_removed' => ':num унусур ба таври доими дур шуд.|:num унусур ба таври доими дур шуданд.',
        
        'restored' => ':num унсур барқарор шудаанд.|:num унсурҳо барқарор шудаанд.',

        'remove_error' => 'Унсурро дур кардан наметавонад. :error',
        
        'copy_error' => 'Ба коллексия наметавонад нусхабардори кунад. :error',
        
        'copy_completed_all' => 'Хамаи санадхо дохил шуданд ба :collection',
        'copy_completed_some' => '{0} Санад дохил нагардид зеро алакай дар коллексияи ":collection"|[1,Inf] мавчуд аст :count Санад илова гардид ба :collection, бокимонда  :remaining алакай дар ин коллексия хастанд :collection',
        
        'restore_error' => 'Сандро баркарол кардан наметавонад. :error',
        
        // 'make_public' => ':num санад нашр шуданд дар шабакаи K-Link |:num санад дастрас хастанд дар шабакаи K-Link.',
        
        // 'make_public_error' => 'Амалиёт нашр аз сабаби хатогӣ ба анҷом расонида нашуд. :error',
        // 'make_public_error_title' => 'Наметавонад дар шабакаи K-Link нашр шавад ',
        
        // 'make_public_success_text_alt' => 'Санад ба таври умуми дар шабакаи K-Link дастрас аст ',
        // 'make_public_success_title' => 'Нашркуни ба анчом расид',

        'adding_title' => 'Санад илова карда шуда истодаас…',
        'adding_message' => 'Лутфан интизор шавед, санадҳо ба коллексия илова карда мешаванд ...',
        'added_to_collection' => 'Илова шуд',
        'some_added_to_collection' => '{0}Санад илова нашуд|[1,Inf]Баъзе санадхо илова нашуданд',
        
        'add_to_error' => 'Ба коллекция илова карда намешавад',
        
        // 'making_public_title' => 'Нашр шуда истодааст…',
        // 'making_public_text' => 'Лутфан, то санадҳо дар шабакаи K-Link дастрас карда шаванд, интизор шавед..',
    
        // 'make_public_change_title_not_available' => 'Имконияти тағир додани унвон пеш аз Нашр ҳоло дастрас нест.',

        // 'make_public_all_collection_dialog_text' => 'Ҳамаи санадҳо дар ин коллексия ба таври умум дар шабакаи K-Link дастрас карда мешаванд. (барои бартараф кардан дар берун зер кунед)',
        // 'make_public_inside_collection_dialog_text' => 'Ҳамаи санадҳо дар дохили ":item" ба таври умум дар шабакаи K-Link дастрас карда мешаванд. (барои бартараф кардан дар берун зер кунед)',
        
        // 'make_public_dialog_title' => 'Нашр ":item" дар шабакаи K-Link',
        // 'make_public_dialog_title_alt' => 'Нашр дар шабакаи K-Link',
        
        // 'publish_btn' => 'Нашр!',
        // 'make_public_empty_selection' => 'Лутфан, санадҳоеро, ки мехоҳед дар шабакаи K-Link дастрас намоед, интихоб намоед.',
        
        // 'make_public_dialog_text' => 'Шумо ":item" ба таври умуми дар шабакаи K-Link нашр мекунед. (барои бартараф кардан дар берун зер кунед)',
        // 'make_public_dialog_text_count' => 'Шумо :count санадро бо дастрасии умуми дар шабакаи K-Link нашр мекунед. (барои бартараф кардан дар берун зер кунед)',
    ],

    'create' => [
        'page_breadcrumb' => 'Сохтан',
        'page_title' => 'Санади навро созед',
    ],

    'edit' => [
        'page_breadcrumb' => 'Таҳрири :document',
        'page_title' => 'Таҳрири :document',

        'title_placeholder' => 'Номи санад',

        'abstract_label' => 'Шарҳи мундариҷа',
        'abstract_placeholder' => 'Шарҳи мундариҷаи санад',

        'authors_label' => 'Муаллифон',
        'authors_help' => 'Муаллифон бояд ҳамчун бо аломати вергул аз хам чудо номнавис шаванд <code>Ному насаб &lt;mail@something.com&gt;</code>',
        'authors_placeholder' => 'Муаллифони санад (ном ва номи падар<mail@something.com>)',

        'language_label' => 'Забон',

        'last_edited' => 'Таҳрири охирин <strong>:time</strong>',
        'created_on' => 'Сохта шуд  <strong>:time</strong>',
        'uploaded_by' => 'Ворид кард <strong>:name</strong>',

        'public_visibility_description' => 'Санад ба ҳамаи ташкилотхо дар шабакаи K-Link дастрас мешавад',
        
        
        'not_index_message' => 'Санад ҳанӯз дар K-Link нашр нашудааст. Лутфан кӯшиш кунед <button type="submit">Индексатсия кунед</button> ё бо администратор муроҷиат намоед.',
        'not_fully_uploaded' => 'Воридкунии ин санад ҳанӯз идома дорад.',
        'preview_available_when_upload_completes' => 'Пешнамоиш баъди воридкунии дастрас мешавад',
    ],

    'update' => [
        'error' => 'Санад навсоз нашуд. Ҳеҷ чиз иваз карда нашудааст. :error',
        
        'removed_from_title' => 'Аз коллексия хориҷ карда шуд',
        'removed_from_text' => 'Санад аз коллексияи ":collection" хорич шуд',
        'removed_from_text_alt' => 'Санад аз коллексияи хорич шуд',
        
        'cannot_remove_from_title' => 'Аз коллесия хориҷ карда намешавад',
        'cannot_remove_from_general_error' => 'Агар мушкилот боқӣ монд, лутфан ба адмнистратори K-Box муроҷиат кунед.',

    ],
    
    'restore' => [
        
        'restore_dialog_title' => 'Барқарор кунед :document?',
        'restore_dialog_text' => 'Шумо ":document" барқарор карданиед ',
        'restore_dialog_title_count' => 'Барқарор кунии :count санад?',
        'restore_dialog_text' => 'Шумо ":document" барқарор карданиед ',
        'restore_dialog_text_count' => 'Шумо :count санадро барқарор карданиед',
        'restore_dialog_yes_btn' => 'Бале, барқарор кун!',
        'restore_dialog_no_btn' => 'Не, Бекор кун',
        
        'restore_success_title' => 'Барқарор шуд',
        'restore_error_title' => 'Барқароркуни имкон надошт',
        'restore_error_text_generic' => 'Мутаассифона, ман чизе, ки шумо аз кутии партов баровардани будед ичро кардан натавонистам.',
      
        'restoring' => 'Барқароркуни …',
    ],
    
    'delete' => [
        
        'dialog_title' => 'Дур кардани ":document"?',
        'dialog_title_alt' => 'Санадро дур мекунед?',
        'dialog_title_count' => ' :count  санадро дур мекунед?',
        'dialog_text' => 'Шумо мехохед санади :document. дур кунед',
        'dialog_text_count' => 'Шумо мехохед :count санадро дур кунед',
        'deleted_dialog_title' => ':document дур шуд',
        'deleted_dialog_title_alt' => 'Дур шуд',
        'cannot_delete_dialog_title' => 'Санади ":document" дур мекунед!',
        'cannot_delete_dialog_title_alt' => 'Дур кара намешавад!',
        'cannot_delete_general_error' => 'Санад бо сабабе дур нашуд, лутфан ба администратор муроҷиат кунед.',
    ],

    'permanent_delete' => [
        
        'dialog_title' => 'Дур кунии доимӣ ":document"?',
        'dialog_title_alt' => 'Санад барои доими дур мекунед?',
        'dialog_title_count' => 'Дур кунад :count санадро?',
        'dialog_text' => 'Шумо дар мархилаи дур кунии  :document карор доред. Ин амалиет боз гирифта намешавад.',
        'dialog_text_count' => 'Шумо дар холи дуркунии  :count санад хастед. Ин амалиет боз гирифта намешавад..',
        'deleted_dialog_title' => ':document ба таври доими дур карда шуд',
        'deleted_dialog_title_alt' => 'Ба таври доими дур шуд',
        'cannot_delete_dialog_title' => 'Ба таври доими дур карда намешавад ":document"!',
        'cannot_delete_dialog_title_alt' => 'Ба таври доими дур нашуд!',
        'cannot_delete_general_error' => 'Санад бо сабабе дур нашуд, лутфан ба администратор муроҷиат кунед.',
    ],

    'preview' => [
        'page_title' => 'Пешнамоиши :document',
        'error' => 'Мутаассифона, мо натавонистем, ки пешнамоиши “:document”. нишон дихем',
        'not_available' => 'Барои ин санад пешнамоиш нишон дода намешавад.',
        'google_file_disclaimer' => ':document файли Google Drive аст, пешнамоиш имкон надорад, ин санадро дар Google Drive кушоед.',
        'google_file_disclaimer_alt' => 'Ин файли Google Drive аст, пешнамоиш дар ин чо нишон дода намешавад.',
        'open_in_google_drive_btn' => 'Дар Google Drive кушоед',
    ],

    'versions' => [

        'section_title' => 'Версияҳои санад',

        'section_title_with_count' => ' :number Версияи санад :number Версияҳои санад',

        'version_count_label' => ':number версия|:number версия',

        'version_number' => 'Версияи :number',

        'version_current' => 'версияи ҷорӣ',

        'new_version_button' => 'Версияи навро ворид кунед',
        
        'new_version_button_uploading' => 'Воридкунии санад…',

        'filealreadyexists' => 'Файле, ки шумо ворид мекунед, аллакай дар K-Box вуҷуд дорад',
    ],

    'messages' => [
        'updated' => 'Тафсилоти санад тағйир ёфт. Раванди тағирот идома дорад, санад ҳанӯз дастрас нест дар натичаи чустучуй.',
        'processing' => 'Санад аз ҷониби K-Box коркард мешавад. Он метавонад дар натиҷаи ҷустуҷӯ дарҳол дастрас бошад..',
        'local_public_only' => 'Дар айни замон танҳо санадхои ташкилот нишон медиҳанд.',
        'forbidden' => 'Шумо қобилияти тағйир додани санадро надоред.',
        'delete_forbidden' => 'Шумо ҳуқуқ надоред, ки санадро дур кунед, лутфан бо менеҷери лоиҳа ё администратор муроҷиат кунед.',
        'delete_public_forbidden' => 'Шумо метавонед санади умумиро дур кунед, лутфан бо K-Linker ё администратор муроҷиат кунед.',
        'delete_force_forbidden' => 'Шумо наметавонед санадро ба таври доими дур кунед, лутфан бо K-Linker ё администратор муроҷиат кунед.',
        'drag_hint' => 'Файлро ба ин чо кашонед то воридкуни сар шавад.',
        'recent_hint_dms_manager' => 'Шумо ҳамаи навсозиҳои санадхоро, ки аз ҷониби ҳар як истифодабарандаи K-Box-и шумо офарида шудааст, мебинед.',
        'no_documents' => 'Санад нест, шумо метавонед санади навро бо истифода аз тугмаи "Созед ё илова кунед" дар боло ё бо зеркашӣ ва ба инҷо баргардонед.',
    ],
    
    
    'trash' => [
        
        'clean_title' => 'Кутии партовро тоза кунад?',
        'yes_btn' => 'Бале, Тоза кун!',
        'no_btn' => 'Не, бекор кун',

        'empty_trash' => 'Дар кутии партов хеч чиз нест',
        
        'empty_all_text' => 'Ҳамаи санадҳо дар кутии партовхо доиман тоза карда мешаванд. Ин амалиёт файлҳо, нусхахо, коллексияҳо ва мубодилотро дур мекунад. Ин амал боз гардонида намешавад.',
        'empty_selected_text' => ' Шумо дар холи дур кунии доимии санадхои интихоб шуда карор доред.. Хамаи файлхо, нусхахо, мубодилот хам дур мешаванд. Ин амал боз гардонида намешавад',
        
        'cleaned' => 'Кутии партов тоза шуд',
        'cannot_clean' => 'Таза намешавад',
        'cannot_clean_general_error' => 'Дар вакти тозакуни мушкиле пеш омад, лутфан ба администратор мурочиат кунед.',
    ],
    
    
    'upload' => [
        'folders_dragdrop_not_supported' => 'Браузери шумо нусхабардории ҷузвдонро пуштибонӣ намекунад.',
        'error_dialog_title' => 'Хатогии воридкунии файл',
        
        'max_uploads_reached_title' => 'Мутаассифона, аммо шумо бояд каме интизор шавед',
        'max_uploads_reached_text' => 'Мо метавонем танҳо як миқдори файлро кор карда барорем, пас лутфан пеш аз илова кардани файли дигар, илтимос каме сабр дошта бошем.',
        
        'all_uploaded' => 'Ҳамаи файлҳо бомуваффақият ворид гадиданд.',
        
        'upload_dialog_title' => 'Ворид кардан',
        'page_title' => 'Ворид кардан',
        'dragdrop_not_supported' => 'Браузери шумо ин намуди амалиети воридкуниро пуштибонӣ намекунад.',
        'dragdrop_not_supported_text' => 'Шумо метавонед санади навро бо истифода аз тугмаи "Сохтан" ё "Ворид кардан" ворид созед ',
        'remove_btn' => "Файлро дур кун", //this is the little link that is showed after the file upload has been processed
        'cancel_btn' => 'Воридкуниро бекор кунед', //for future use
        'cancel_question' => 'Оё шумо боварӣ доред, ки ин воридкуниро бекор кунед?',  //for future use
        'outside_project_target_area' => 'Лутфан файли худро ба тарафи Лоиҳа кашед то ворид шавад.',
        'empty_file_error' => 'Файли холӣ, лутфан файлеро, ки ақаллан як калима дорад, вокрид кунед.',
    ],
];
