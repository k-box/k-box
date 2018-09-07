<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mail messages
    |--------------------------------------------------------------------------
    |
    |
    */
    
    'logo_text' => 'K-Box',
    'footer_disclaimer' => "<a href=\":url\">:instance</a> мүчөсү катары бул маалыматты алып жатасаыз",
    'footer_help' => "<a href=\":url\">Колдоо кызматы</a>",


    'welcome' => [

        /*
            Welcome mail template
         */

        'disclaimer' => 'Бул катты каттоо жөнүндө маалыматтарды камтыйт. Сураныч, коопсуз жерге сактап коюнуз.',
        'welcome' => 'Кош келеңиз, :name,',
        'credentials' => 'Сиздин уюмдун K-Box менен колдонуу <br/> колдонуучу атын <strong>:mail</strong><br/>жана пароль <strong>:password</strong>',
        'credentials_alt' => 'Сиздин уюмдун K-Box төмөнкүдөй маалыматтарды колдоно аласыз',
        'username' => 'Колдонуучунун аты **:mail**',
        'password' => 'пароль `:password`',


        'login_button' => '<a href=":link">Системага кирүү</a>',
        'login_button_alt' => 'Кирүү',
        

    ],

    'password_reset_subject' => 'Паролду жаңыртуу үчүн шилтемени сурадыңыз',
    
     'sharecreated' => [
        /**
         * Strings for the mails.shares.created email template. Used when a share to a document is created
         */
        'subject' => 'K-Box - :user предоставил вам прямой доступ к :title',
        'shared_document_with_you' => ':user колдонуучу документ менен бөлүштү',
        'shared_collection_with_you' => ':user коллекция менен бөлүштү',
        'title_label' => 'Аты',
    ],

    
];
