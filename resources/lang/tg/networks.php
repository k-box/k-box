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

    'klink_network_name' => 'Шабакаи K-Link',
    'menu_public_klink' => 'K-Link',
    'menu_public' => ':network',
    'menu_public_hint' => 'Санадхои дастрас дар шабакаи :network нигох кун',

    'make_public' => 'Ба хама дастрас намо',
    'publish_to_short' => 'Нашр кунед',
    'publish_to_long' => 'Нашр кунед дар :network',

    
    'publish_to_hint' => 'Якчанд санадро интихоб кунед, ки дар  :network нашр шаванд',
    

    'publish_btn' => 'Нашр мекунед!',

    'settings' => [
        'section' => 'Ба шабака хамрох шавед',
        'section_help' => 'Дар ин ҷо шумо метавонед танзим намоед,ки чӣ тавр K-Box-ро ба шабака пайваст кунед',
        'enabled' => 'Барои нашр санад дар шабака ичозат аст',
        'debug_enabled' => 'Ислокуниро (Debug) барои пайвастшави ба шабака фаъол созед',
        'username' => 'Истифодабаранда барои аутентификасия дар шабака истифода мешавад',
        'password' => 'Пароле, ки барои аутентификасия бо Шабака истифода мешавад',
        'url' => 'URL -и Нуқтаи Дохилшавии Шабака',
        'name_en' => 'Номи шабака (версияи англиси)',
        'name_ru' => 'Номи шабака (версияи руси)',
        'name_section' => 'Номи шабака',
        'name_section_help' => 'Барои шабака ном интихоб кунед, он ҳангоми нашр кардани санад ё коллексияҳо истифода мешавад. Агар холати нагузоштани номи шабака, номи “K-Link Public Network" истифода мешавад',
    ],

    'made_public' => ':num санад аз тарики  :network|:num нашр шуда дастрас хастанд дар :network.',
        
    'make_public_error' => 'Амали нашр бо сабаби хатоги анҷом наёфт. :error',
    'make_public_error_title' => 'Дар :network нашр кардан наметавонад',
    
    'make_public_success_text_alt' => 'Ин санадхо акнун дар шабакаи :network дастар шуданд ',
    'make_public_success_title' => 'Нашр куни анчом ефт',

    'making_public_title' => 'Нашр куни дар :network...',
    'making_public_text' => 'Лутфан интизор шавед санадхо дар :network дастрас мегарданд',

    'make_public_change_title_not_available' => 'Имконияти тағир додани ном пеш аз Нашр ҳоло дастрас нест.',

    'make_public_all_collection_dialog_text' => 'Шумо ҳамаи санадҳои ин коллексияро дар шабакаи :network  дастрас мегардонед. (барои бозгашт дар берун зер кунед)',
    'make_public_inside_collection_dialog_text' => 'Шумо ҳамаи санадҳои дар дохили ":item" дар шабакаи :network  дастрас мегардонед. (барои бозгашт дар берун зер кунед)',
    
    'make_public_dialog_title' => ' ":item" дар  :network нашр кунед',
    'make_public_dialog_title_alt' => 'Нашр дар :network',
    
    
    'make_public_empty_selection' => 'Лутфан, санадҳоеро, ки мехоҳед дар :network дастрас гардонед, интихоб кунед.',
    
    'make_public_dialog_text' => 'Шумо ":item" дар шабакаи :network дастрас мегардонед. (барои бекор кардан дар берун зер кунед)',
    'make_public_dialog_text_count' => 'Шумо :count санадро дар шабакаи :network дастрас мегардонед. (барои бекор кардан дар берун зер кунед)',

];