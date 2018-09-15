<?php

/*
|--------------------------------------------------------------------------
| Geo Plugin Routes
|--------------------------------------------------------------------------
*/

Route::get('/', 'GeoPluginSettingsController@index')->name('settings');

Route::put('/', 'GeoPluginSettingsController@update')->name('settings.store');

Route::get('/providers', 'GeoPluginMapProvidersController@index')->name('mapproviders');

// Route::put('/providers', 'GeoPluginMapProvidersController@update')->name('mapproviders.store');
