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
    
    'delete' => [
        
        'dialog_title' => 'Өчүр ":collection"?',
        'dialog_title_alt' => 'Коллекцияны өчүрүүнү каалайсызбы?',
        'dialog_text' => '":collection" коллекциясын өчүрөсүз. Ичиндеги документтер өчүрүлбөйт.',
        'dialog_text_alt' => 'Тандалган коллекцияны өчүрөсүз. Ичиндеги документтер өчүрүлбөйт.',
        
        'deleted_dialog_title' => '":collection" коллекциясы өчүрүлдү',
        'deleted_dialog_title_alt' => 'Өчүрүлдү',
        
        'cannot_delete_dialog_title' => '":collection" коллекциясын өчүрүү мүмкүн эмес',
        'cannot_delete_dialog_title_alt' => 'Өчүрүлгөн жок',
        
        'cannot_delete_general_error' => 'Өчүрүлгөн жок',
        
        'forbidden_trash_personal_collection' => '":collection" коллекциясын башка колдонуучу түзгөн, өчүрүүгө мүмкүн эмес',
        'forbidden_delete_shared_collection' => 'Башка колдонуучу ":collection" коллекциясын бөлүшкөн, өчүрүүгө мүмкүн эмес',
        'forbidden_delete_personal_collection' => '":collection" коллекциясын башка колдонуучу түзгөн, өчүрүүгө мүмкүн эмес',
        'forbidden_delete_collection' => ':collection коллекцияны өчүрүүгө укугуңуз жок',
        'forbidden_delete_project_collection' => ':collection коллекциясын өчүрүү мүмкүн эмес',
        'forbidden_delete_project_collection_not_creator' => '":collection" коллекциясын башка колдонуучу түзгөн, өчүрүүгө мүмкүн эмес',
        'forbidden_delete_project_collection_not_manager' => '":collection" коллекциясын долбоордун менеджери өчүрө алат',
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
