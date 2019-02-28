<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Generic Errors Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used inside for rendering error messages
    |
    */

    'unknown' => 'Хатогии умумии номаълум дар дархост',

    'upload' => [
        'simple' => 'Хатогии воридкуни :description',
        'filenamepolicy' => 'Файли :filename аз руйи Конвентсияи номнависӣ ногмгузори нашудааст.',
        'filealreadyexists' => ' Файли :filename алакай вучуд дорад.',
    ],

    'filealreadyexists' => [
        'generic' => 'Санади :name алакай дар K-Box бо номи  <strong>":title"</strong> мавчуд аст.',
        'incollection' => 'Санад алакай дастарс аст дар ин чо <a href=":collection_link"><strong>":collection"</strong></a> бо номи <strong>":title"</strong>',
        'incollection_by_you' => 'Шумо алакай ин санадро ворид намудед бо номи  <strong>":title"</strong> дар <a href=":collection_link"><strong>":collection"</strong></a>',
        'by_you' => 'Ин санадро хамчун  <strong>":title"</strong> ворид намудед',
        'revision_of_document' => 'Санадеро, ки ваорид мекунед нусхаи  <strong>":title"</strong>, аз тарафи :user (:email) воридшуда мебошад',
        'revision_of_your_document' => '  Ин санад нусхаи мавчудбудаи санади шумо бо номи  <strong>:title</strong> мебошад',
        'by_user' => 'Ин санад алакай ба K-Box аз тарафи :user (:email) ворид шудааст.',
        'in_the_network' => 'Ин санад алакай дар инчо дастрас аст <strong>:network</strong> бо номи <strong>":title"</strong>. Ворид шуд аз тарафи :institution',
    ],

    'group_already_exists_exception' => [
        'only_name' => 'Коллексия бо номи ":name" аллакай вуҷуд дорад.',
        'name_and_parent' => 'Коллексияи  ":name" дар ":parent" аллакай вуҷуд дорад.',
    ],
    
    'generic_text' => 'Ой! чизи ғайричашмдошт рӯй дод.',
    'generic_text_alt' => 'Ой! чизи ғайричашмдошт рӯй дод. :error',
    'generic_title' => 'Ой!',

    'reindex_all' => 'Аз сабаби хатогиҳо, амали хамаро аз нав индекс гузоштан ба анҷом нарасидааст. Ба гузориш нигаред ё ба администратор муроҷиат кунед.',

    'token_mismatch_exception' => 'Мумкин вакти сесияи шумо ба охир расид, лутфан сахфаро аз нав боз кунед ва сипас бо кори худ идома диҳед. Ташаккур.',

    'not_found' => 'Чизе ки Шумо ҷустуҷӯ кардед, ёфта наметавонад.',
    
    'document_not_found' => 'Санадеро, ки Шумо ҷустуҷӯ кардед ёфт нашуд е дур карда шудааст.',

    'forbidden_exception' => 'Шумо ба ин саҳифа дастраси надоред.',
    'forbidden_edit_document_exception' => 'Шумо санадро таҳрир карда наметавонед.',
    'forbidden_see_document_exception' => 'Шумо санадро дида наметавонед, зеро он санади шахсии истифодабаранда аст.',
    
    'kcore_connection_problem' => 'Пайвастшавӣ ба K-Link анчом наёфт.',

    'fatal' => 'Хатогии ногузир :reason',

    'panels' => [
        'title' => 'Ой! чизи ғайричашмдошт рӯй дод.',
        'prevent_edit' => 'Шумо таҳрир карда наметавонед :name',
    ],

    'group_edit_institution' => "Шумо наметавонед гурӯҳҳои сатҳи ташкилотро тағйир диҳед.",
    'group_edit_project' => "Шумо наметавонед коллексияи лоиҳаро тағйир диҳед.",
    'group_edit_else' => "Шумо наметавонед гурӯҳҳои дигарро таҳрир кунед.",

    '503_title' => 'Нигоҳдории K-Link',
    '503_text' => ' <strong>K-Box</strong> дар айни хол<br/><strong>дар нигоҳдории аст</strong><br/><small> ва хеле зуд боз мегардад :)</small>',

    '500_title' => 'Хатогии - K-Link ',
    '500_text' => 'Ой! Чизе <strong>бад</strong><br/>ва ғайричашмдошт <strong>рӯй дод</strong>,<br/>мо хеле мутаассифем.',

    '404_title' => 'Дар K-Link ёфт нашуд',
    '404_text' => 'Ой! Шояд <strong>сахфае</strong><br/>ки шумо ҷустуҷӯ кардед<br/><strong>дигар вучуд надорад</strong>.',
    
    '401_title' => 'Шумо метавонед саҳифаи K-Link-ро бинед',
    '401_text' => 'Ой! Шумо <strong>наметавонед</strong> ба ин саҳифа ворид шавед<br/>чунки<strong>Иҷозатнома</strong> дар ин сатх надоред.',

    'login_title' => 'Лутфан ба K-Box ворид шавед',
    'login_text' => 'Барои дидани санад Шумо бояд ба K-Box ворид шавед.',
    
    '403_title' => 'Шумо иҷозати вуруд ба саҳифаро надоред',
    '403_text' => 'Ой! Шумо <strong>наметавонед</strong> ба ин саҳифа ворид шаваед<br/>чунки Шумо<strong>Иҷозатнома</strong> дар ин сатх надоред.',

    '405_title' => 'Усул дар K-Link иҷозат дода намешавад',
    '405_text' => 'Дигар маро чунин нагуед',
    
    '413_title' => 'Андозаи санад аз ҳад зиёд',
    '413_text' => 'Файле, ки шумо ворид кардан мехоҳед, аз ҳадди андозаи максимали зиед аст.',
    
    'klink_exception_title' => 'Хатогии хизматрасонии K-Link',
    'klink_exception_text' => 'Ба K-Link пайваст карда натавонист.',
    
    'reindex_failed' => 'Ҷустуҷӯ бо тағйироти охирин мувофик нест, барои маълумоти иловагӣ ба гурухи кумакрасон муроҷиат кунед.',
    
    'page_loading_title' => 'Мушкилоти боркуни',
    'page_loading_text' => 'Саҳифаҳои боркунӣ суст ва баъзе амалиётҳо наметавонанд дастрас бошанд, лутфан саҳифаро нав кунед.',
    
    'dragdrop' => [
        'not_permitted_title' => 'Амали кашидан ва рахо кардан дастрас нест',
        'not_permitted_text' => 'Шумо амалиёти кашидан ва рахо карданро иҷро карда наметавонед.',
        'link_not_permitted_title' => 'Амали линкро кашидан дастрас нест',
        'link_not_permitted_text' => 'Дар айни замон шумо наметавонед линкхоро кашидан ва рахо кунед.',
    ],

    'support_widget_opened_for_you' => 'Мо ба тугмачаи дастгирии барои шумо кушодем, лутфан ба мо нависед, то ки мо хатогиҳоро тафтиш кунем. Ташаккур барои дастгирии шумо.',
    'go_back_btn' => 'Ман фаҳмидам. Маро аз ин ҷо берун кунед.',
    
    'preference_not_saved_title' => 'Имтиёз хифз нашудааст',
    'preference_not_saved_text' => 'Мутаассифона, мо натавонистам, ки имтиёзи Шуморо хифз кунем, бори дигар такрор кунед.',

    'generic_form_error' => 'Шумо хатогиҳо доред, лутфан онҳоро пеш аз идома додан ислоҳ кунед',

    'oldbrowser' => [
        'generic' => 'Браузери шумо кухна шудааст. Барои беҳтар кардан, браузери худро азнав кунед.',
        'ie8' => 'Браузери шумо (Internet Explorer 8) кухна шудааст. Ин камбудиҳои ҳамаи хусусиятҳои K-Linkро намоиш намедиҳанд. Барои кори беҳтар, браузери худро нав кунед.',
        'nosupport' => 'Браузер аз тарафи K-Box пуштибонӣ намешавад.',
        
        'more_info' => 'Иттилои иловагӣ',
        'dismiss' => 'Рад кардан',
    ],

];

