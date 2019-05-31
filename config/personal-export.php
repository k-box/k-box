<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disk to use to store personal exports
    |--------------------------------------------------------------------------
    */

    'disk' => env('PERSONAL_EXPORT_DISK', 'personal-data-exports'),

    /*
    |--------------------------------------------------------------------------
    | The amount of days the exports will be available.
    |--------------------------------------------------------------------------
    */
    'delete_after_days' => 5,

];
