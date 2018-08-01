<?php

use KBox\Http\Controllers\Plugins\PluginsController;

/*
|--------------------------------------------------------------------------
| Plugin Routes
|--------------------------------------------------------------------------
*/

Route::group(['as' => 'administration.', 'prefix' => 'administration'], function () {

    // Plugins related routes
    Route::resource('/plugins', PluginsController::class, ['only' => ['index'/*, 'show', 'edit'*/, 'update', 'destroy']]);
});
