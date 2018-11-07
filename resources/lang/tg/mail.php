<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mail messages
    |--------------------------------------------------------------------------
    |
    |
    */

    // Global layout elements
    'logo_text' => 'K-Box',
    'footer_disclaimer' => "Шумо ин паёмро баьди кушодани аккаунт дар инчо   <a href=\":url\">:instance</a> гирифтед",
    'footer_help' => "<a href=\":url\">Кумак</a>",

    'welcome' => [

        /*
            Welcome mail template
         */

        'disclaimer' => 'Ин  E-Mail мушахассоти даромадан ба система дорад, лутфан орно махфуз нигох доред.',
        'welcome' => 'Хуш омадед :name',
        'credentials' => 'Акнун шумо метавонед дастраси ба K-Box институти худ бо ёрии ин <br/>username <strong>:mail</strong><br/>password <strong>:password</strong>',
        'credentials_alt' => 'Анкун шумо метавонед ба K-Box ташкилоти худ бо маълумоти зерин дастраси дошта бошед',
        'username' => 'истифодабаранда **:mail**',
        'password' => 'парол `:password`',

        'login_button' => '<a href=":link">Логин</a>',
        'login_button_alt' => 'Логин',
        

    ],
    
    'password_reset_subject' => 'Шумо дархости аз нав кардани пароли худ дар  K-Box-ро кардед',

    'sharecreated' => [
        /**
         * Strings for the mails.shares.created email template. Used when a share to a document is created
         */
        'subject' => 'K-Link - :user бо шумо :title мубодила намуд',
        'shared_document_with_you' => ':user бо шумо санад мубодила намуд',
        'shared_collection_with_you' => ':user коллекция бо шумо мубодила кард',
        'title_label' => 'Ном',
    ],

    'duplicatesnotification' => [
        'subject' => 'Мо дар боргузории охирони Шумо якчанд санади такрори ёфтем',
        'greetings' => 'Ҳангоми санҷиши такрори, мо муайян кардем, ки баъзе аз санадҳои шумотакрори санадхои мавчудбуда мебошанд. Агар такори ин санадхо ба шумо писанд аст ба ин паём аҳамият надиҳед.',
        'action' => 'Такрори санадхои охирини худро бинед',
    ],

];
