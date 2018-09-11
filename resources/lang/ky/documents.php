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
        
        'name' => 'Аты',
        'added_by' => 'Кошулган',
        'language' => 'Тили',
        'added_on' => 'Кошулган',
        'last_modified' => 'Өзгөртүлдү',
        'indexing_error' => 'Документ K-Link тармагында индексациялоо болгон жок',
        'private' => 'Жабык',
        'shared' => 'Файл менен бөлүшүү',
        'is_public' => 'Ачык документтер',
        'is_public_description' => 'Бул документ K-Link тармагындагы уюмдарга жеткиликтүү',
        'trashed' => 'Документ корзинада',
        'klink_public_not_mine' => 'Өзгөртүү киргизүүгө мүмкүн эмес, файл K-Link тармагында жарыяланган',
    ],

    'page_title' => 'Документтер',

    'menu' => [
        'all' => 'Баары',
        'public' => 'K-Link тармагы',
        'private' => 'Баары',
        'personal' => 'Документтерим',
        'starred' => 'Тандалма',
        'shared' => 'Бөлүшкөн',
        'recent' => 'Акыркы',
        'trash' => 'Корзина',
        'not_indexed' => 'Индексациялоосуз',
        'recent_hint' => 'Акыркы документтериңиз',
        'starred_hint' => 'Керектүү жана кызыктуу документтериңиз',
    ],
        'sort' => [
        'sorted_by' => ':sort боюнча иреттөө',
        'type_project_name' => 'Долбоордун аталышы',
        'type_search_relevance' => 'Издегичтин релеванттыгы',
        'type_updated_at' => 'Өзгөртүү датасы',
        ],
        
        'filtering' => [
        'date_range_hint' => 'Керектүү убакыт диапазону',
        'items_per_page_hint' => 'Баракчадагы элементтердин саны',
        'today' => 'Бүгүн',
        'yesterday' => 'Кечээтен бери',
        'currentweek' => 'Акыркы 7 күн',
        'currentmonth' => 'Акыркы 30 күн',
    ],

    'visibility' => [
        'public' => 'Ачык',
        'private' => 'Жабык',
    ],

    'type' => [
        // See here for better understanding of the russian translation rules https://github.com/symfony/symfony/issues/8698
        // 'нет яблок|есть одно яблоко|есть %count% яблока|есть %count% яблок'
        // no apples | have one apple | have %count% apples | have %count% apples
        // 0 | 1-4 | 5+
        // 21 | 22-24 | 25+
        /**

        - If the number is 1, or the number ends in the word 1 (example: 1, 21, 61) (but not 11), then you should use the first case
        - If the number, or the last digit of the number is 2, 3 or 4, (example: 22, 42, 103, 4) (but not 12, 13 & 14), then you should use the second case
        - If the number ends in any other digit you should use the 3rd case. All the 'teens'  fit in to this catagory (11, 12, 13, 14, 15,16,17,18,19). Any number ending with 0 (including 0 itself) also fits into this category
*/
        'web-page' => 'веб баракча',
        'document' => 'документ',
        'spreadsheet' => 'таблица',
        'presentation' => 'презентация',
        'uri-list' => 'URL тизме',
        'image' => 'сүрөт',
        'geodata' => 'гео-маалымат',
        'text-document' => 'тексттик документ',
        'video' => 'видео',
        'archive' => 'архив',
        'PDF' => 'PDF',
    ],

    'empty_msg' => '<strong>:context</strong> ичинде документ жок',
    'empty_msg_recent' => '<strong>:range</strong> үчүн документ жок',

    'bulk' => [

        'removed' => ':num документ корзинага салынды',
        
        'permanently_removed' => ':num документ өчүрүлдү',
        
        'restored' => ':num документ кайра калыбына келтирилди',

        'remove_error' => 'Өчүрүүгө мүмкүн эмес :error',
        
        'copy_error' => 'Коллекцияга көчүрүүгө мүмкүн эмес :error',
        
        'copy_completed_all' => 'Баардык документтер :collection коллекциясына кошулду',
        
        // used when not all the documents you were adding to a collection has been added
        'copy_completed_some' => '{0}Ни один документ не был добавлен, т.к. они уже хранились в ":collection"|[1,Inf] Добавленых документов :count, оставшиеся :remaining уже находились в :collection',
        
        'restore_error' => 'Документти калыбына келтирүү мүмкүн эмес :error',
        

        'adding_title' => 'Документтерди кошуу...',
        'adding_message' => 'Документтериңиз коллекцияга кошулуп жатат...',
        'added_to_collection' => 'Кошулду',
        'some_added_to_collection' => '{0}Кошулган жок|[1,Inf]Кээ бир документтер кошулган жок',
        
        'add_to_error' => 'Коллекцияга коошууга мүмкүн эмес',
        
    ],

    'create' => [
        'page_breadcrumb' => 'Түзүү',
        'page_title' => 'Жаңы документ түзүү',
    ],

    'edit' => [
        'page_breadcrumb' => ':document документти өзгөртүү',
        'page_title' => ':document документти өзгөртүү',

        'title_placeholder' => 'Документтин аталышы',

        'abstract_label' => 'Кыскача мазмуну',
        'abstract_placeholder' => 'Кыскача мазмунун жазыңыз',

        'authors_label' => 'Авторлор',
        'authors_help' => 'Авторлор үтүр мене бөлүнүп, <code>аты фамилиясы &lt;mail@something.com&gt;</code> форматта көрсөтүлүш керек',
        'authors_placeholder' => 'Документтин авторлору (аты, фамилиясы <mail@something.com>)',
        
        'language_label' => 'Тил',

        'last_edited' => '<strong>:time</strong> өзгөртүлдү',
        'created_on' => '<strong>:time</strong> кошулду',
        'uploaded_by' => '<strong>:name</strong> жүктөдү',

        'public_visibility_description' => 'Документ K-Link тармагынын баардык мүчөлөрүнө жеткиликтүү болот',
        
        
        'not_index_message' => 'Документ K-Link тармагында жеткиликтүү эмес. <button type="submit">переиндексировать его</button> баскычын басыңыз же болбосо администратор менен байланышыңыз.',
        'not_fully_uploaded' => 'Документ жүктөө аягына чыга элек',
        'preview_available_when_upload_completes' => 'Документти көрүү мүмкүнчүлүгү жүктөө бүткөндөн кийин жеткиликтүү болот',
   
   
        'license' => 'Лицензия',
        'license_help' => 'Лицензия аркылуу автордук укуктун ээси башка адамдарга анын чыгармачылыгын колдонууга мүмкүндүк берет',
        'license_choose_help_button' => 'Лицензия тандоо боюнча жардам маалымат',
        
        'copyright_owner' => 'Автордук укуктун ээси',
        'copyright_owner_help' => 'Автордук укуктун ээси жөнүндө маалымат',
        
        'copyright_owner_name_label' => 'Аты',
        'copyright_owner_email_label' => 'Электрондук почта',
        'copyright_owner_website_label' => 'Веб-сайт',
        'copyright_owner_address_label' => 'Адрес',

    
    ],

    'update' => [
        'error' => 'Бул документти жаңыртууга мүмкүн эмес :error',
        
        'removed_from_title' => 'Коллекция алынды',
        'removed_from_text' => 'Документ ":collection" коллекциядан алынды',
        'removed_from_text_alt' => 'Документ коллекциядан алынды',
        
        'cannot_remove_from_title' => 'Коллекциядан алууга мүмкүн эмес',
        'cannot_remove_from_general_error' => 'Коллекциядан алууга мүмкүн эмес, K-Box администраторуна кайрылыңыз',

    ],
    
    'restore' => [
        
        'restore_dialog_title' => 'Ушул документти :document кайра калыбына келтирүүнү каалайсызбы?',
        'restore_dialog_text' => 'Бул документти ":document" кайра калыбына келтиресиз',
        'restore_dialog_title_count' => 'Ушул документтерди :count кайра калыбына келтирүүнү каалайсызбы?',
        'restore_dialog_text' => 'Бул документти ":document" кайра калыбына келтиресиз',
        'restore_dialog_text_count' => 'Ушул элементтерди :count кайра калыбына келтиресиз',
        'restore_dialog_yes_btn' => 'Макул',
        'restore_dialog_no_btn' => 'Жок',
        
        'restore_success_title' => 'Даяр',
        'restore_error_title' => 'Кайра калыбына келтирилген жок',
        'restore_error_text_generic' => 'Документ корзинадан чыгарылган жок',
      
        'restoring' => 'Кайра калыбына келтирүү...',
    ],
    
    'delete' => [
        
        'dialog_title' => 'Ушул документти ":document" корзинага салууну каалайсызбы?',
        'dialog_title_alt' => 'Документти корзинага салууну каалайсызбы?',
        'dialog_title_count' => 'Ушунча документтерди :count корзинага салууну каалайсызбы?',
        'dialog_text' => 'Ушул документти :document корзинага салып жатасыз',
        'dialog_text_count' => 'Ушунча документтерди :count корзинага салып жатасыз',
        'deleted_dialog_title' => 'Бул документ :document корзинада',
        'deleted_dialog_title_alt' => 'Даяр',
        'cannot_delete_dialog_title' => 'Бул документти ":document" өчүрүүгө мүмкүн эмес',
        'cannot_delete_dialog_title_alt' => 'Корзинага салуу мүмкүн эмес',
        'cannot_delete_general_error' => 'Ката кетти, администратор менен байланышыңыз',
    ],

    'permanent_delete' => [
        
        'dialog_title' => 'Документти ":document"? толук өчүрүүнү каалайсызбы?',
        'dialog_title_alt' => 'Документти толук өчүрүүнү каалайсызбы?',
        'dialog_title_count' => ':count документти өчүрүүнү каалайсызбы?',
        'dialog_text' => 'Сиз :document документти толугу менен өчүргөнү жатасыз, аны кайра калыбына келтире албайсыз',
        'dialog_text_count' => 'Сиз :count документти толугу менен өчүргөнү жатасыз, аны кайра калыбына келтире албайсыз',
        'deleted_dialog_title' => ':document документ өчүрүлдү',
        'deleted_dialog_title_alt' => 'Даяр',
        'cannot_delete_dialog_title' => 'Документти ":document" өчүрүүгө мүмкүн эмес',
        'cannot_delete_dialog_title_alt' => 'Мүмкүн эмес',
        'cannot_delete_general_error' => 'Документ өчүрүлгөн жок, сураныч, администраторуңуз менен байланышыңыз',
    ],

    'preview' => [
        'page_title' => 'Документти :document көрүү',
        'error' => 'Документти ":document" көрүүгө мүмкүн эмес',
        'not_available' => 'Бул документти көрүү мүмкүн эмес',
        'google_file_disclaimer' => 'Бул документти :document Google Диск гана аркылуу көрсөңүз болот',
        'google_file_disclaimer_alt' => 'Бул файл Google Дисктен, аны көрүүгө мүмкүн эмес',
        'open_in_google_drive_btn' => 'Google Диск аркылуу көрүү',
        'video_not_ready' => 'Видеону бир нече секундадан кийин көрсөңүз болот',
    ],

    'versions' => [

        'section_title' => 'Версиялар',

        'section_title_with_count' => ':number версия',

        'version_count_label' => ':number версия',

        'version_number' => 'версия :number',

        'version_current' => 'Азыркы',

        'new_version_button' => 'Жаңы версиясын жүктөө',
        
        'new_version_button_uploading' => 'Жүктөө...',

        'filealreadyexists' => 'Бул версия системада бар',
    ],

    'messages' => [
        'updated' => 'Документ жаңыланды',
        'processing' => 'Документ системага кошулуп жатат',
        'local_public_only' => 'Уюмдун ачык документтери көрсөтүлгөн',
        'forbidden' => 'Документтерди өзгөртүүгө мүмкүнчүлүгүңүз жок',
        'delete_forbidden' => 'Документтерди өчүрүүгө мүмкүнчүлүгүңүз жок, сураныч, администраторго кайрылыңыз',
        'delete_public_forbidden' => 'Ачык документти өчүрүүгө мүмкүнчүлүгүңүз жок, сураныч, администраторго кайрылыңыз',
        'delete_force_forbidden' => 'Документт толук өзгөртүүгө мүмкүнчүлүгүңүз жок, сураныч, администраторго кайрылыңыз',
        'drag_hint' => 'Файлды жуктөө үчүн аны ушул жерге мышка менен алып келиңиз',
        'recent_hint_dms_manager' => 'Бул жерде акыркы жүктөөлөр жана өзгөртүүлөр көрсөтүлгөн',
        'no_documents' => 'Документтер жок, жүктөө үчүн «Жүктөө» кнопкасын колдонуңуз',
    ],
    
     'trash' => [
        
        'clean_title' => 'Корзинаны тазалоону каалайсызбы?',
        'yes_btn' => 'Ооба',
        'no_btn' => 'Жок',
        
        'empty_trash' => 'Корзина бош',
        
        'empty_all_text' => 'Баардык документтер толугу менен өчүрүлөт корзинадан',
        'empty_selected_text' => 'Документтерди корзинадан толугу менен өчүргөнү жатасыз',
        
        'cleaned' => 'Корзина тазаланды',
        'cannot_clean' => 'Корзинаны тазалоо мүмкүн эмес',
        'cannot_clean_general_error' => 'Катта кетти. Сураныч, администратор менен байланышыңыз.',
    ],
    
    
    'upload' => [
        'folders_dragdrop_not_supported' => 'Браузериңиз папкаларды көчүрө албайт',
        'error_dialog_title' => 'Жүктөө учурунда ката кетти',
        
        'max_uploads_reached_title' => 'Жүктөө...',
        'max_uploads_reached_text' => 'Мы можем обрабатывать пока только маленькие файлы. Пожалуйста, проявите немного терпения перед очередным добавлением файлов.',
        
        'all_uploaded' => 'Баардык файлдар жүктөлдү',
        
        'upload_dialog_title' => 'Жүктөө',
        'page_title' => 'Жүктөө',
        'dragdrop_not_supported' => 'Файлды алып-ташуу функциясын браузериңиз колдобойт',
        'dragdrop_not_supported_text' => 'Сураныч, «Жүктөө» кнопкасын колдонуңуз',
        'remove_btn' => "Корзинага",
        'cancel_btn' => 'Жүктөөнү токтотуу',
        'cancel_question' => 'Жүктөөнү токтотууну каалайсызбы?',
        'outside_project_target_area' => 'Сураныч, файлды жүктөө үчүн, аны проектке алып таштаңыз',
        'empty_file_error' => 'Бош документ. Сураныч, ичинде маалымат болгон файлды жүктөңүз',
    ],
];
