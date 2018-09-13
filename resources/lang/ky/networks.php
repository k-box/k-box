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

    'klink_network_name' => 'K-Link тармагы',
    'menu_public_klink' => 'K-Link тармагы',
    
    'menu_public' => ':network',
    'menu_public_hint' => ':network тармактагы документтерди көрүңүз',

    'make_public' => 'Жарыялоо',
    'publish_to_short' => 'Жарыялоо',
    'publish_to_long' => ':network тармака жарыялоо',

    
    'publish_to_hint' => ':network тармака жарыялоо үчүн документтерди тандаңыз',
    

    'publish_btn' => 'Жарыялоо',

    'settings' => [
        'section' => 'Тармака кошулуу',
        'section_help' => 'Тармака кошулуу орнотуулары',
        'enabled' => 'Тармака документтерди жарыялоо',
        'debug_enabled' => 'Байланышта каталарды оңдоого уруксат берүү',
        'username' => 'Байланышты аутентификациялоо үчүн пайдаланган колдонуучунун аты',
        'password' => 'Байланыш үчүн сырсөз',
        'url' => 'Тармака кошулуу түйүнүнүн URL адреси',
        'name_en' => 'Англис тилинде',
        'name_ru' => 'Орус тилинде',
        'name_section' => 'Тармактын аты',
        'name_section_help' => 'Тармактын атын жазыңыз, ал жарыялоо учурунда көрсөтүлөт. Ансыз «K-Link ачык тармагы» деп коюлат',
        'streaming_section' => 'Видео көрсөтүү',
        'streaming_section_help' => 'Видеону жарыялоо үчүн видео көрсөтүү кызматын жандырыңыз',
        'streaming_service_url' => 'Видео көрсөтүү кызматтын URL маалыматты',
    ],

    'made_public' => ':num документ тармака жарыяланды',
        
    'make_public_error' => 'Жарыялоо учурунда ката кетти :error',
    'make_public_error_title' => ':network тармака жарыялоо мүмкүн эмес',
    
    'make_public_success_text_alt' => 'Документтер :network тармагында ачык',
    'make_public_success_title' => 'Жарыяланды',

    'making_public_title' => ':network тармака жарыяланып жатат...',
    'making_public_text' => 'Документтер :network тармагына жарыяланып жатат, күтүп туруңуз',

    'make_public_change_title_not_available' => 'Файлдын атын өзгөртүү жарыялоодон алдын мүмкүн эмес',

    'make_public_all_collection_dialog_text' => 'Бул коллекциянын документтерин :network тармагында ачык кыласыз',
    'make_public_inside_collection_dialog_text' => '":item" коллекциянын документтерин :network тармагында ачык кыласыз',
    
    'make_public_dialog_title' => ':network тармагына ":item" жарыялоо',
    'make_public_dialog_title_alt' => ':network тармака жарыялоо',
    
    
    'make_public_empty_selection' => ':network тармагына жарыялоо үчүн документтерди тандаңыз',
        
    'make_public_dialog_text' => 'Документ ":item" :network тармагында ачык болот',
    'make_public_dialog_text_count' => ':count документти :network тармагында ачык кыласыз',
    
    'publication_error_copyright' => 'Документти жарыялоодон алдын автор жөнүндө маалыматты көрсөтүңүз',

];
