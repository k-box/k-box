<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Collections Language Lines
    |--------------------------------------------------------------------------
    |
    |
    */

    'collections' => [
        'title'        => 'Коллекция',
        'personal_title' => 'Коллекцияларым',
        'private_title' => 'Проекттер',
        'description'   => 'Коллекция аркылуу документтериңиз өз жайында болот',
        
        'empty_private_msg' => 'Азыркы убакытта долбоорлор жок',

    ],

    'create_btn' => 'Түзүү',
    'save_btn' => 'Сактоо',
    'loading' => 'Сактоо...',

    'panel_create_title' => 'Жаңы коллекция',

    'panel_edit_title' => 'Өзгөртүү <strong>:name</strong>',

    'created_on' => 'түзүлгөн',
    'created_by' => 'түзүлгөн',

    'private_badge_label' => 'Жеке документтердин коллекциясы',

    'group_icon_label' => 'Коллекция',

    'empty_msg' => 'Коллекциялар жок',

    'form' => [
        'collection_name_placeholder' => 'Коллекциянын атын жазыңыз',
        'collection_name_label' => 'Аты',

        'parent_label' => '<strong>:parent</strong> коллекциянын ичинде',
        'parent_project_label' => '<strong>:parent</strong> долбоордун ичинде',

        'make_public' => 'Коллекцияны проектин мүчүлөрү үчүн ачык кылуу',
        'make_private' => 'Коллекцияны жеке кылуу',
    ],
    
    
    
    'people' => [
        
        'page_title' => 'Группалар',
            
        'no_users' => 'Колдонуучуларды группага кошуу мүмкүн эмес. Сураныч администратор менен байланышыңыз',
        
        'available_users' => 'Жеткиликтүү колдонуучулар',
        'available_users_hint' => 'Группага кошуу үчүн колдонуучуну бул тизмеден алып группа таштаңыз',
        
        'remove_user' => 'Группадан алып салуу',
        
        'saving' => 'Сактоо...',
        
        'invalidargumentexception' => 'Извините, опреация не может быть выполнена. :exception',
        
        'group_name_already_exists' => 'Группа с таким названием уже существует.',
        'create_group_dialog_title' => 'Группаны түзүү',
        'create_group_dialog_text' => 'Группанын аты:',
        'create_group_dialog_placeholder' => 'Классная группа',
        'create_group_error_title' => 'Группа түзүлгөн жок',
        'create_group_generic_error_text' => 'Группаны түзүү мүмкүн эмес',
        
        'cannot_add_user_dialog_title' => 'Ката',
        'cannot_add_user_dialog_text' => 'Колдонуучуну кошууга мүмкүн эмес',
        
        'user_already_exists' => 'Колдонуучуну ":name" группада бар',
        
        'delete_dialog_title' => '":name" өчүрүүнү каалайсызбы?',
        'delete_dialog_text' => '":name" группаны таптакыр өчүрүүнү каалайсызбы?',
        'delete_error_title' => 'Группаны өчүрүү мүмкүн эмес',
        'delete_generic_error_text' => 'Группаны өчүрүү мүмкүн эмес',
        
        'remove_user_dialog_title' => '":name" алып салууну каалайсызбы?',
        'remove_user_dialog_text' => '":name" колдонуучуну ":group" группадан алып салууну каалайсызбы?',
        'remove_user_error_title' => 'Колдонуучуну группадан алып салуу мүмкүн эмес',
        'remove_user_generic_error_text' => 'Колдонуучуну группадан алып салуу мүмкүн эмес',
        
        'rename_dialog_title' => '":name" атын өзгөртүүнү каалайсызбы?',
        'rename_dialog_text' => 'Группанын аты:',
        'rename_error_title' => 'Группанын аты өзгөртүлгөн жок',
        'rename_generic_error_text' => 'Группанын атын өзгөртүү мүмкүн эмес',
    ],
    
    
    'delete' => [
        
        'dialog_title' => 'Өчүр :collection?',
        'dialog_title_alt' => 'Коллекцияны өчүрүүнү каалайсызбы?',
        'dialog_text' => ':collection коллекцияны өчүрөсүз. Ичиндеги документтер өчүрүлбөйт.',
        'dialog_text_alt' => 'Тандалган коллекцияны өчүрөсүз. Ичиндеги документтер өчүрүлбөйт.',
        
        'deleted_dialog_title' => ':collection коллекциясы өчүрүлдү',
        'deleted_dialog_title_alt' => 'Өчүрүлдү',
        
        'cannot_delete_dialog_title' => '":collection" коллекцияны өчүрүү мүмкүн эмес',
        'cannot_delete_dialog_title_alt' => 'Өчүрүлгөн жок',
        
        'cannot_delete_general_error' => 'Өчүрүлгөн жок',
        
        'forbidden_delete_collection' => ':collection коллекцияны өчүрүүгө укугуңуз жок',
        'forbidden_delete_project_collection' => ':collection коллекцияны өчүрүү мүмкүн эмес',
    ],
    
    'move' => [
        'moved' => '":collection" коллекция жылдырылды',
        'moved_alt' => 'Даяр',
        'moved_text' => 'Коллекция жылдырылды, визуализацияны жаңыртып жатабыз...',
        'error_title' => ':collection коллекцияны жылдырууга мүмкүн эмес',
        'error_title_alt' => 'Коллекцияны жылдыруу мүмкүн эмес',
        'error_text_generic' => 'Катага байланыштуу жылдыруу аяктаган жок, администратор менен байланышыңыз',
        'error_not_collection' => 'Жылдыруу коллекциялар үчүн гана жеткиликтүү',
        'error_same_collection' => 'Коллекцияны жылдыруу мүмкүн эмес',
        'move_to_title' => '":collection" коллекцияга жылдырайынбы?',
        'move_to_project_title' => '":collection" коллекцияга жылдырайынбы?',
        'move_to_project_title_alt' => 'Проектке жылдырайынбы?',
        'move_to_project_text' => 'Жеке коллекцияны проектке жылдырганы жатасыз. ":collection" коллекция жана ичиндеги коллекциялар проекттин баардык мүчүлөрүнө жеткиликтүү болот.',
        'move_to_personal_title' => 'Коллекцияны жеке кылайынбы?',
        'move_to_personal_text' => 'Коллекцияны проекттен алып, жеке кылганы жатасыз. ":collection" коллекция проекттин мүчүлөрүнө көрүнбөй калат.',
    ],
    
    'access' => [
        'forbidden' => '":name" ачууга укугуңуз жок',
        'forbidden_alt' => 'Коллекцияны ачууга укугуңуз жок',
    ],

    'add_documents' => [
        'forbidden' => 'Документти ":name" коллекцияга кошууга укугуңуз жок',
        'forbidden_alt' => 'Документти коллекцияга кошууга укугуңуз жок',
    ],

    'remove_documents' => [
        'forbidden' => 'Документтерди ":name" коллекциядан өчүрүүгө укугуңуз жок',
        'forbidden_alt' => 'Документтерди коллекциядан өчүрүүгө укугуңуз жок',
    ],

];
