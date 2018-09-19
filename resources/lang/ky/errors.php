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

    'unknown' => 'Суроодо белгисиз ката кетти',

    'upload' => [
        'simple' => 'Жүктөө учурунда ката :description кетти',
        'filenamepolicy' => 'Файл :filename документ аталыш эрежелерине туура келбейт',
        'filealreadyexists' => 'Файл :filename системага жүктөлгөн',
    ],
    
        'filealreadyexists' => [
        'generic' => 'Бул документ :name K-Box-го <strong>":title"</strong> деп мурда жүктөлгөн',
        'incollection' => 'Бул документ <a href=":collection_link"><strong>":collection"</strong></a> деген коллекцияда <strong>":title"</strong> деа жүктөлгөн',
        'incollection_by_you' => 'Бул документти ушул <a href=":collection_link"><strong>":collection"</strong></a> коллекцияга <strong>":title"</strong> деп жүктөгөнсүз',
        'by_you' => 'Бул документти <strong>":title"</strong> деп жүктөгөнсүз',
        'revision_of_document' => 'Жүктөлүп жаткан документ, ушул колдонуучу :user (:email) жүктөгөн документтин <strong>":title"</strong> жаңы версиясы',
        'revision_of_your_document' => 'Документ ичиндеги маалымат бул копия менен <strong>":title"</strong> окшош', 
        'by_user' => 'Бул колдонуучу :user (:email) документти K-Box-го мурда жүктөгөн',
        'in_the_network' => 'Документ <strong>:network</strong> тармагында <strong>":title"</strong> деген аталышы менен, ушул уюм :institution жүктөгөн',
    ],

    'group_already_exists_exception' => [
        'only_name' => 'Так ушундай коллекция ":name" мурда кошулган',
        'name_and_parent' => 'Так ушундай ":name" коллекция ":parent" ичинде мурда түзүлгөн',
    ],

    'generic_text' => 'Ката кетти',
    'generic_text_alt' => 'Ката кетти :error',
    'generic_title' => 'Ката',

    'reindex_all' => 'Бир катадан улам, жаңыдан индексациялоо мүмкүн эмес, администратор менен байланышыңыз', //logs as list of errors

    'token_mismatch_exception' => 'Сессияңыз бүтүп калды, браузер баракчасын жаңылантыңыз',

    'not_found' => 'Ресурс табылган жок',
    
    'document_not_found' => 'Керектүү документ табылган жок же өчүрүлгөн',

    'forbidden_exception' => 'Баракча жеткиликтүү эмес',
    'forbidden_edit_document_exception' => 'Документти өзгөртүү мүмкүн эмес',
    'forbidden_see_document_exception' => 'Документти көрүү мүмкүн эмес',
    
    'kcore_connection_problem' => 'K-Link тармагына кошулуу мүмкүн эмес.',
    
    'fatal' => 'Ката кетти :reason',

    'panels' => [
        'title' => 'Күтүлбөгөн ката кетти',
        'prevent_edit' => 'Бул атты :name өзгөртүү мүмкүн эмес',
    ],

    'import' => [
        'folder_not_readable' => 'Бул папканы :folder окуу мүмкүн эмес',
        'url_already_exists' => 'Ушул вебсайттан бул файл (:url) мурда системага сакталган',
        'download_error' => 'Документти ":url" сактоого мүмкүн эмес :error',
    ],

    'group_edit_institution' => "Бул группаларды өзгөрттүү мүмкүн эмес",
    'group_edit_project' => "Бул коллекцияларды өзгөртүүгө мүмкүн эмес",
    'group_edit_else' => "Бул группага өзгөрттүүлөрдү киргизүү мүмкүн эмес",

    '503_title' => 'K-Box тейлөө',
    '503_text' => '<strong>K-Box</strong> <br/><strong>техникалык тейлөөдө</strong><br/>',

    '500_title' => 'Ката - K-Box',
    '500_text' => '<strong>Күтүлбөгөн</strong><br/> жана жаман <strong>ката кетти</strong>,<br/>',

    '404_title' => 'K-Box-до табылган жок',
    '404_text' => '<br/>Бул <br/><strong>баракча</strong> <strong>өчүрүлгөн</strong>',
    
    '401_title' => 'K-Box баракчасын көрө албайсыз',
    '401_text' => '<strong>Бул баракчаны</strong> просмотреть страницу<br/> <strong>көрө албайсыз</strong>',

    '403_title' => 'Бул баракчаны көрүүгө уруксатыңыз жок',
    '403_text' => '<strong>Бул</strong> баракчаны көрө албайсыз, <br/>уруксатыңыз <strong>жок</strong>',

    '405_title' => 'Бул методту K-Box-то колдонууга мүмкүн эмес',
    '405_text' => 'Не называй меня больше так.',
    
    '413_title' => 'Докумен өтө чоң',
    '413_text' => 'Жүктөлүп жаткан файлдын көлөмү максималдуу өлчөмдөн ашып кетти',

    'klink_exception_title' => 'K-Link сервистеринин катасы',
    'klink_exception_text' => 'K-Link сервистерине байланыш катасы',
    
    'page_loading_title' => 'Проблема загрузки',
    'page_loading_text' => 'Баракча жай жүктөлүп жатат, кээ бир функциялар иштебей калышы мүмкүн, баракчаны кайрадан жүктөңүз',

    'dragdrop' => [
        'not_permitted_title' => 'Азырынча файлды алып барып таштоо функциясы жеткиликтүү эмес',
        'not_permitted_text' => 'Файлды алып таштай албайсыз',
        'link_not_permitted_title' => 'Ссылкаларды алып барып таштоо жеткиликтүү эмес',
        'link_not_permitted_text' => 'Азырынча ссылкаларды алып барып таштай албайсыз',
    ],
    
    'reindex_failed' => 'Издөөдө акыркы өзгөрттүүлөр көрсөтүлгөн жок, колдоо кызматы менен байланышыңыз',

    'support_widget_opened_for_you' => 'Сиз үчүн колдоо кызматы жеткиликтүү, ката изилдөө үчүн жазыңыз',
    'go_back_btn' => 'Артка',
    
    'preference_not_saved_title' => 'Колдонуучунун орнотуулары сакталган жок',
    'preference_not_saved_text' => 'Тилеке каршы, колдонуучунун орнотуулары сакталган жок. Сураныч, бир аздан кийин аракет кылып көрүңуз.',

    'generic_form_error' => 'Каталар кетти, аларды оңдоңуз',

    'oldbrowser' => [
        'generic' => 'Браузердин эски версиясын колдонуп жатасыз, системаны толук иштетүү үчүн аны жаңылантыңыз',
        'ie8' => 'Internet Explorer 8 колдонуп жатасыз, ал тармак менен коопсуз байланышка кепилдик бербейт. Андан сырткары, K-Link-тин кээ бир функциялары иштебейт. Сураныч, аны жаңылантыңыз.',
        'nosupport' => 'K-Box бул браузерде иштебейт',
        
        'more_info' => 'Кошумча маалымат',
        'dismiss' => 'Жашыруу',
    ],



];
