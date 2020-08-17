<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Picture
    |--------------------------------------------------------------------------
    |
    | This is the url of the picture to show on login and registration screen.
    | It can be absolute or relative. If is a resource outside the K-Box
    | application domain it will be downloaded and cached locally.
    */

    'picture' => env('KBOX_APPEARANCE_PICTURE', 'https://images.unsplash.com/photo-1563654727148-d7e9d1ed2a86?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2850&q=80'),
    
    /*
    |--------------------------------------------------------------------------
    | Color
    |--------------------------------------------------------------------------
    |
    | The color to show on login and registration screen instead of the picture.
    */

    'color' => env('KBOX_APPEARANCE_COLOR', null),
];
