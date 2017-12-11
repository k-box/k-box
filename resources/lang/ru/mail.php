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
    'footer_disclaimer' => "Вы получили это сообщение, потому что являетесь участником <a href=\":url\">:instance</a>",
    'footer_help' => "<a href=\":url\">Помощь</a>",


    'welcome' => [

        /*
            Welcome mail template
         */

        'disclaimer' => 'Это письмо содержит информацию о вашей учетной записи. Пожалуйста, сохраните его в безопасном месте.',
        'welcome' => 'Добро пожаловать, :name,',
        'credentials' => 'Пройдите в K-Box вашей организации, используя <br/> имя пользователя <strong>:mail</strong><br/>и пароль <strong>:password</strong>',
        'credentials_alt' => 'Доступ к K-Box вашей организации возможен с помощью следующих данных',
        'username' => 'имя пользователя **:mail**',
        'password' => 'пароль `:password`',


        'login_button' => '<a href=":link">Войти в систему</a>',
        'login_button_alt' => 'Войти',
        

    ],

    'password_reset_subject' => 'Вы запросили ссылку для сброса пароля для вашего аккаунта в K-Box',
    
     'sharecreated' => [
        /**
         * Strings for the mails.shares.created email template. Used when a share to a document is created
         */
        'subject' => 'K-Box - :user предоставил вам прямой доступ к :title',
        'shared_document_with_you' => ':user поделился с вами документом',
        'shared_collection_with_you' => ':user поделился с вами коллекцией',
        'title_label' => 'Название',
    ],

    
];
