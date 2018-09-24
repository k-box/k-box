<?php

/*
|--------------------------------------------------------------------------
| Geo Plugin Routes
|--------------------------------------------------------------------------
*/

Route::get('/', 'GeoPluginSettingsController@index')->name('settings');

Route::put('/', 'GeoPluginSettingsController@update')->name('settings.store');

Route::get('/providers', 'GeoPluginMapProvidersController@index')->name('mapproviders');

Route::post('/providers', 'GeoPluginMapProvidersController@store')->name('mapproviders.store');

Route::get('/providers/create', 'GeoPluginMapProvidersController@create')->name('mapproviders.create');

Route::put('/providers/{id}', 'GeoPluginMapProvidersController@update')->name('mapproviders.update');

Route::get('/providers/{id}/edit', 'GeoPluginMapProvidersController@edit')->name('mapproviders.edit');
