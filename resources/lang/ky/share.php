<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shared page Language Lines
    |--------------------------------------------------------------------------
    |
    */

    'page_title' => 'Бөлүшкөн документтер',
    
    'share_btn' => 'Бөлүшүү',

    'share_panel_title' => ':num элементке мүмкүндүк берүү',
    
    'share_panel_title_alt' => '":what" мүмкүндүк берүү|":what" мүмкүндүк берүү жана :count башка файлдарга|":what" мүмкүндүк берүү жана :count башка файлдарга',

    'share_created_msg' => ':num мүмкүндүк жасалды',

    'with_label' => 'Мүмкүндүк берүү',

    'what_label' => 'Файл менен бөлүшүп жатасыз',

    'empty_with_me_message' => 'Эч ким документ бөлүшкөн жок',

    'empty_by_me_message' => 'Документ же коллекция менен бөлүшкөн жоксуз',

    'shared_by_me_title' => 'Мен бөлүшкөн файлдар',
    'shared_by_me_count' => ':num элементке мүмкүндүк берилди',

    'shared_with_me_title' => 'Мени менен бөлүшкөн',
    
    'shared_with_label' => 'Сиз бөлүштүңүз',
    'shared_by_label' => 'Бөлүштү',
    
    'bulk_destroy' => 'Документке байланыш өчүрүлдү|Кээбир документтердин байланышын өчүрүүгө мүмкүн эмес<br/>:errors',
    'removed' => 'Мүмкүндүк жок',
    'remove_error' => 'Жабууга мүмкүн эмес :Ошибка',
    'unshare' => 'Бөлүшүүнү жокко чыгаруу',
    'unsharing' => 'Бөлүшүү жокко чыгырылып жатат...',
    'remove' => 'Өчүрүү',
    'removing' => 'Өчүрүү...',
    
    'share_link_section' => 'Шилтеме менен бөлүшүү',
    'download_link_copy' => 'Жүктөө үчүн шилтемени көчүрүү',
    'document_link_copy' => 'Шилтемени көчүрүү',
    'preview_link_copy' => 'Документти көрүү үчүн шилтемени көчүрүү',
    'document_link_copy_multiple' => 'Шилтемелерди көчүрүү',
    'send_link' => 'Шилтемени жөнөтүү',
    'send_link_multiple' => 'Шилтемелерди жөнөтүү',
    
    'link_copied_to_clipboard' => 'Шилтеме көчүрүлдү. Коюу үчүн CTRL+V баскычтарын колдонуңуз',

    'shared_on' => 'Дата',
    
    'dialog' => [
        'title' => 'Бөлүшүү орнотуулары',
        'subtitle_single' => ':what', // only one element to share
        'subtitle_multiple' => ':what жана башка :count файл', // X and 1 other|X and 2 others
        'share_created' => 'Бөлүндү',
        'collection_shared' => 'Коллекция бөлүндү',
        'collection_shared_text' => 'Коллекция бөлүндү',
        'document_shared' => 'Бөлүндү',
        'document_shared_text' => 'Документ бөлүндү',
        'multiple_selection_not_supported' => 'Бирден көп файлдар менен бөлүшүү мүмкүн эмес',
        'publish_multiple_selection_not_supported' => 'Бир мезгилде бир эле файлды жарыялоо мүмкүн',
        'publish_collection_not_supported' => 'Коллекцияны жарыялоо функциясы даяр эмес',

        'section_access_title' => 'Көрүү мүмкүндүгү',
        'section_linkshare_title' => 'Көчүрүүгө шилтеме',
        'section_linkshare_title_alternate' => 'Көчүрүүгө шилтеме',
        'section_publish_title' => 'Жарыялоо',

        'linkshare_hint' => 'Катталган колдонуучулар гана файлды көрө алат',
        'linkshare_multiple_selection_hint' => 'Бирден көп файлдарды катталган колдонуучулар менен гана бөлүшсө болот. Каттоосу жок колдонуучулар үчүн бир файл тандаңыз.',
        'linkshare_members_only' => 'Катталган колдонуучулар үчүн',
        'linkshare_public' => 'Каттоосу жок колдонуучуларга',

        'published' => ':network жарыяланды',
        'not_published' => ':network жарыяланган жок',
        'publishing' => 'Жарыяланып жатат...',
        'publishing_failed' => 'Жарыяланган жок',
        'unpublishing' => 'Жарыя токтотулуп жатат...',
        'publish_collection' => 'Коллекциядагы баардык документтер жарыяланат',
        'publish_already_in_progress' => 'Документтерди жарыялоо башталды',

        'document_is_shared' => 'Көрө алат:',
        'collection_is_shared' => 'Көрө алат:',
        'users_already_has_access' => ':num колдонуучу',
        'users_already_has_access_alternate' => '{0} Сиз гана|{1} :num колдонуучу|',
        
        'users_already_has_access_with_public_link' => '{0} Каалаган колдонуучу көрө алат|{1} Сиз жана ачык шилтемени алган колдонуучулар|[2,Inf]:num колдонуучу жана ачык шилтемени алган колдонуучулар',
        'document_already_accessible_by_all_users' => 'Документ системанын баардык колдонуучуларына ачык',
        'collection_already_accessible_by_all_users' => 'Коллекция системанын баардык колдонуучуларына ачык',

        'add_users' => 'Бөлүшүү',
        'select_users' => 'Атыны жазыңыз...',

        'access_by_direct_share' => 'Түз мүмкүндүк алуу',
        'access_by_project_membership' => 'Проект ":project"',
        'access_by_project_membership_hint' => '":project" проекттин мүчөсү катары документти көрө аласыз',
    ],
    'publiclinks' => [
        'public_link' => 'Ачык шилтеме',
        'already_exist' => 'Бул документке :name ачык шилтеме түзүлгөн',
        'delete_forbidden_not_your' => 'Шилтемени өчүрүү мүмкүн эмес',
        'edit_forbidden_not_your' => 'Шилтемени оңдоо мүмкүн эмес',
    ],
];
