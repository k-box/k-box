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
        'title'  => 'Коллексияҳо',
        'personal_title' => 'Коллексияҳои ман',
        'private_title' => 'Лоиҳаҳо',
        'description'   => 'Коллексия барои мураттаб кардани санадхои шумо кумак мекунад.',
        
        'empty_private_msg' => 'Холо лоиха вучуд надорад.',

    ],

    'create_btn' => 'Сохтан',
    'save_btn' => 'Сабт',
    'loading' => 'Сабти коллексия…',

    'panel_create_title' => 'Сохтани коллексияи нав',

    'panel_edit_title' => 'Таҳрири коллексияи <strong>:name</strong>',

    'created_on' => 'Сохта шуд',
    'created_by' => 'Сохта шуд аз тарафи',

    'private_badge_label' => 'Коллексияҳои шахсӣ',

    'group_icon_label' => 'Коллексия',

    'empty_msg' => 'Коллексия мавчуд нест. Коллексияро созед.',

    'form' => [
        'collection_name_placeholder' => 'Номи коллексия',
        'collection_name_label' => 'Номи коллексия',

        'parent_label' => 'Коллесияи болотар: <strong>:parent</strong>',
        'parent_project_label' => 'Дар коллексияи лоиха: <strong>:parent</strong>',

        'make_public' => 'Ин коллексия ба истифодабарандагони лоиҳа намоён кунед.',
        'make_private' => 'Ин коллексияро хамчун Шахси ишора кунед',
    ],
    
    
    
    'people' => [
        
        'page_title' => 'Гурӯҳҳо',
            
        'no_users' => 'Истифодабарандаро ба гурӯҳ илова карда наметавонед, лутфан бо администратор муроҷиат кунед ё тасдиқ кунед, ки истифодабарандагон метавонанд мубодилотро гиранд ва бинанд.',
        
        'available_users' => 'Истифодабарандагони дастрас',
        'available_users_hint' => 'Истифодабаранда аз ин ҷо ба гурӯҳе, ки хохед дохил кунед.',
        
        'remove_user' => 'Аз гурӯҳ хорич кунед',
        
        'saving' => 'Сабт…',
        
        'invalidargumentexception' => 'Мутаассифона, амалиёт иҷро намешавад. :exception',
        
        'group_name_already_exists' => 'Гурӯҳи бо ҳамин ном аллакай вуҷуд дорад',
        'create_group_dialog_title' => 'Гурӯҳро созед',
        'create_group_dialog_text' => 'Номи гурӯҳ:',
        'create_group_dialog_placeholder' => 'Гурӯҳи олӣ',
        'create_group_error_title' => 'Сохтани гурӯҳ амали нашуд',
        'create_group_generic_error_text' => 'Гурӯҳ наметавонад сохта шавад инро ҳамаи мо медонем.',
        
        'cannot_add_user_dialog_title' => 'Истифодабарандаро илова карда наметавонад',
        'cannot_add_user_dialog_text' => 'Истифодабаранда наметавонад ба гурӯҳ илова карда шавад. Хатои ғайричашмдошт рух дод.',
        
        'user_already_exists' => 'Истифодабаранда ":name" алакай дар гурух вучуд дорад',
        
        'delete_dialog_title' => 'Дур кун ":name"?',
        'delete_dialog_text' => 'Гурухи ":name" ба таври доими дур мекунед?',
        'delete_error_title' => 'Гурӯҳро нест кардан наметавонанд',
        'delete_generic_error_text' => 'Гурух дур карда намешавад, инро ҳамаи мо медонем',
        
        'remove_user_dialog_title' => 'Дур мекунед ":name"?',
        'remove_user_dialog_text' => 'Дур кун ":name" аз гурухи ":group"?',
        'remove_user_error_title' => 'Истифодабарандаро аз гурух дур карда наметавонад',
        'remove_user_generic_error_text' => 'Истифодабарандаро дур карда наметавонад. инро ҳамаи мо медонем',
        
        'rename_dialog_title' => 'Аз нав номгузории ":name" ?',
        'rename_dialog_text' => 'номи гурух:',
        'rename_error_title' => 'Аз нав номгузори амали нашуд',
        'rename_generic_error_text' => 'Гурух аз нав номгузори нашуд.',
    ],
    
    
    'delete' => [
        
        'dialog_title' => 'Дур мекунед :collection?',
        'dialog_title_alt' => 'Коллексияро дур мекунед?',
        'dialog_text' => 'Шумо холо коллесияи :collection дур мекунед. Танҳо коллексия нест мешавад ва онро аз санадхо хориҷ мекунад. Санадхо нест карда мешаванд.',
        'dialog_text_alt' => 'Шумо холо коллексияи интихобшударо дур мекунед. Танҳо коллексия нест мешавад ва онро аз санадхо хориҷ мекунад. Санадхо нест карда мешаванд.',
        
        'deleted_dialog_title' => ':collection дур карда шуд',
        'deleted_dialog_title_alt' => 'Дур шуд',
        
        'cannot_delete_dialog_title' => 'Дур карда наметавонад ":collection"!',
        'cannot_delete_dialog_title_alt' => 'Дур карда намешавад!',
        
        'cannot_delete_general_error' => 'Унсурҳои мушаххасшуда сабт карда нашуд. Ҳеҷ чиз нест карда шудааст.',
        
        'forbidden_trash_personal_collection' => 'You did not create :collection, therefore you cannot trash it.',
        'forbidden_delete_personal_collection' => 'You did not create :collection, therefore you cannot delete it.',
        'forbidden_delete_collection' => 'Коллексия  :collection дур карда намешавад. Шумо ичозати амалиет гузаронидан аз болои ин коллексия надоред.',
        'forbidden_delete_project_collection' => 'Коллексия :collection дур карда намешавад чунки ин дар лоихае мебошад, ки Шумо ичозати тахрир карданро онро надоред.',
        'forbidden_delete_project_collection_not_creator' => 'You are not the creator of the collection :collection, therefore you cannot delete it.',
        'forbidden_delete_project_collection_not_manager' => 'You are not the manager of the project that contained :collection, therefore you cannot delete it.',
    ],
    
    'move' => [
        'moved' => '":collection" Кучонида шуд',
        'moved_alt' => 'Кучонида шуд',
        'moved_text' => 'Коллексия кучонида шуд, намудиро хозираро дигар мекунем…',
        'error_title' => 'Наметавонад кучад :collection',
        'error_title_alt' => 'Коллексия кучонида наметавонад',
        'error_text_generic' => 'Амалиети кучонидан анчом наефт чунки хатоги рох дода шуд, лутфан ба администратори K-Box мурочиат намоед.',
        'error_not_collection' => 'Амалиети кучонидан танхо ба коллексия гузаронида мешавад.',
        'error_same_collection' => 'Коллексиядо дар дохили худи коллексия кучонидан мумкин нест.',
        'move_to_title' => 'Ба ин чо мекучони ":collection"?',
        'move_to_project_title' => 'Ба ин чо мекучони ":collection"?',
        'move_to_project_title_alt' => 'Ба лоиха мекучонед?',
        'move_to_project_text' => 'Шумо холо коллексияи шахсиро ба Лоиха мекучонед. Ин амал ":collection", ва хамаи субколлексияхои онро ба тамоми истифодабарандагони Лоиха намоен мекунад.',
        'move_to_personal_title' => 'Коллексияро Хусуси мекунед?',
        'move_to_personal_text' => 'Шумо холо коллексияро аз Лоиха ба коллексияи Шахси мекучонед. Коллексияи ":collection" ба дигар истифодабарандагони лоиха намоен намешавад.',

        'errors' => [
            'personal_not_all_same_user' => 'Cannot move ":collection" to your personal. You are not the creator of :collection_cause',
            'personal_not_all_same_user_empty_cause' => 'Cannot move ":collection" to your personal as you are not the creator of it',
            'no_project_collection_permission' => 'You do not have the necessary permission to move a project collection',
            'no_access_to_collection' => 'You do not have access to the collection',
        ],
    ],
    
    'access' => [
        'forbidden' => '":name" Шумо ичозати дастрасӣ надоред.',
        'forbidden_alt' => 'Шумо ба коллексияҳо бо сабабҳои сатхи ичозатдихи дастрасӣ надоред',
    ],
    
    'add_documents' => [
        'forbidden' => 'Шумо ворид кардани санад ба ":name" надоред, чунки ичозати барои ин зарур бударо надоред.',
        'forbidden_alt' => 'Шумо ворид кардани санад ба коллексия надоред, чунки ичозати барои ин зарур бударо надоред.',
    ],
    
    'remove_documents' => [
        'forbidden' => 'Шумо наметавонед санадро дур кунед аз  ":name" чунки ичозати зарури барои ин надоред.',
        'forbidden_alt' => 'Шумо наметавонед санадро аз коллексия дур кунед, чунки ичозати зарури барои ин надоред.',
    ],

];