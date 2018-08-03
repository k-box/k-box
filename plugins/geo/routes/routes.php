<?php

/*
|--------------------------------------------------------------------------
| Geo Plugin Routes
|--------------------------------------------------------------------------
*/

Route::get('/', 'GeoPluginSettingsController@index')->name('settings');

Route::put('/', 'GeoPluginSettingsController@update')->name('settings.store');
