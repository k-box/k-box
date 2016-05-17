<?php

// Project Pokedex


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', ['as' => 'frontpage', 'uses' => 'WelcomeController@index']);

Route::get('home', ['as' => 'dashboard', 'uses' => 'HomeController@index']);

Route::get('visualizationdata', ['as' => 'visualization', 'uses' => 'VisualizationApiController@index']);

/**
 * Search route
 */
Route::get('search/autocomplete', ['as' => 'search-autocomplete', 'uses' => 'SearchController@autocomplete']);
Route::get('search', ['as' => 'search', 'uses' => 'SearchController@index']);
// TODO: search will have more methods on the controller




Route::get('administration', [
	'as' => 'administration.index',
	'uses' => 'Administration\AdministrationDashboardController@index',
	'permission' => KlinkDMS\Capability::MANAGE_DMS]);


Route::get('administration/users/{id}/remove', [
  'as' => 'administration.users.remove',
  'uses' => 'Administration\UserAdministrationController@remove',
  'permission' => KlinkDMS\Capability::MANAGE_USERS]);

Route::get('administration/users/{id}/restore', [
  'as' => 'administration.users.restore',
  'uses' => 'Administration\UserAdministrationController@restore',
  'permission' => KlinkDMS\Capability::MANAGE_USERS]);
  
Route::get('administration/users/{id}/resetpsw', [
  'as' => 'administration.users.resetpassword',
  'uses' => 'Administration\UserAdministrationController@resetPassword',
  'permission' => KlinkDMS\Capability::MANAGE_USERS]);

Route::resource('administration/users', 'Administration\UserAdministrationController');

Route::resource('administration/messages', 'Administration\MessagingController', ['only' => ['create', 'store']]);

Route::resource('administration/institutions', 'Administration\InstitutionsController');

Route::controller('administration/network', 'Administration\NetworkAdministrationController', [
     'getIndex' => 'administration.network.index',
]);

Route::controller('administration/storage', 'Administration\StorageAdministrationController', [
    'getIndex' => 'administration.storage.index',
    'getReindexAll' => 'administration.storage.reindexstatus',
    'postReindexAll' => 'administration.storage.reindexall',
    'postNaming' => 'administration.storage.naming',
]);

Route::controller('administration/languages', 'Administration\LanguageAdministrationController', [
    'getIndex' => 'administration.languages.index',
]);

Route::controller('administration/maintenance', 'Administration\MaintenanceAdministrationController', [
    'getIndex' => 'administration.maintenance.index',
]);

Route::controller('administration/mail', 'Administration\MailAdministrationController', [
    'getIndex' => 'administration.mail.index',
    'postStore' => 'administration.mail.store',
    'getTest' => 'administration.mail.test',
]);

Route::resource('administration/settings', 'Administration\SettingsAdministrationController', ['only' => ['index', 'store']]);


Route::resource('documents/groups', 'Document\GroupsController');

Route::get('documents/recent', [ 
        'uses' => 'Document\DocumentsController@recent',
        'as' => 'documents.recent',
    ]);
Route::get('documents/trash', [ 
        'uses' => 'Document\DocumentsController@trash',
        'as' => 'documents.trash',
    ]);
Route::get('documents/notindexed', [ 
        'uses' => 'Document\DocumentsController@notIndexed',
        'as' => 'documents.notindexed',
    ]);

Route::get('documents/shared-with-me', [ 
        'uses' => 'Document\DocumentsController@sharedWithMe',
        'as' => 'documents.sharedwithme',
    ]);

Route::resource('documents/starred', 'Document\StarredDocumentsController', 
	['only' => ['index', 'show', 'store', 'destroy']]);

Route::get('documents/{institution}/{local_id}', [ 
        'uses' => 'Document\DocumentsController@showByKlinkId',
        'as' => 'documents.by-klink-id',
    ])->where(['local_id' => '(?!edit)[A-Za-z0-9]+', 'institution' => '[A-Za-z0-9]+']);

Route::get('documents/{visibility}', [ 
        'uses' => 'Document\DocumentsController@index',
        'as' => 'documents.visibility',
    ])->where(['visibility' => '(public|private|personal)']);

Route::put('documents/import/clearcompleted',
  [ 
        'uses' => 'Document\ImportDocumentsController@clearCompleted',
        'as' => 'documents.import.clearcompleted',
    ]);

Route::put('documents/restore',[ 
    'uses' => 'Document\BulkController@restore',
    'as' => 'documents.bulk.restore',
  ]);

Route::post('documents/remove',[ 
      'uses' => 'Document\BulkController@destroy',
      'as' => 'documents.bulk.remove',
  ]);
  
Route::post('documents/makepublic',[ 
      'uses' => 'Document\BulkController@makePublic',
      'as' => 'documents.bulk.makepublic',
  ]);

Route::post('documents/copy',[ 
      'uses' => 'Document\BulkController@copyTo',
      'as' => 'documents.bulk.copyto',
  ]);

Route::resource('documents/import', 'Document\ImportDocumentsController', 
	['names' => ['index' => 'import'], 'only' => ['index', 'store', 'destroy', 'update']]);



Route::resource('documents', 'Document\DocumentsController');

Route::put('shares/deletemultiple',[ 
    'uses' => 'SharingController@deleteMultiple',
    'as' => 'shares.deletemultiple',
  ]);

Route::get('shares/group/{id}', [ 
        'uses' => 'SharingController@showGroup',
        'as' => 'shares.group',
    ]);



Route::resource('shares', 'SharingController');


/**
 * User profile controller
 */
Route::post('profile/options',[ 
      'uses' => 'UserProfileController@update',
      'as' => 'profile.update',
  ]);
Route::resource('profile', 'UserProfileController', ['only' => ['index', 'store']]);


Route::resource('people', 'People\PeopleGroupsController');


\Route::get('projects/{slug}/{language?}', [ 
    'uses' => '\Klink\DmsMicrosites\Controllers\MicrositeController@show',
    'as' => 'projects.site',
])->where(['slug' => '(?!create)[a-z\\-]+', 'language' => '^[a-z]{2}$']); // slug cannot contain 'create' as generates a conflict with projects.create route

Route::resource('projects', 'Projects\ProjectsController');



/*
|--------------------------------------------------------------------------
| External Accessible Routes
|--------------------------------------------------------------------------
|
| External route for get the document content and thumbnail
|
*/

Route::get('klink/{id}/{action}',[ 
        'uses' => 'KlinkApiController@show',
        'as' => 'klink_api',
    ])->where(['id' => '[0-9A-Za-z]+', 'action' => '(thumbnail|document)']);

/*
|--------------------------------------------------------------------------
| Authentication & Password Reset Controllers
|--------------------------------------------------------------------------
|
| These two controllers handle the authentication of the users of your
| application, as well as the functions necessary for resetting the
| passwords for your users. You may modify or remove these files.
|
*/

// Login and Logout

Route::get('auth/login', [ 
        'uses' => 'Auth\AuthController@getLogin',
        'as' => 'auth.login',
    ]);

Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Password reset link request routes...
Route::get('password/email', [ 
        'uses' => 'Auth\PasswordController@getEmail',
        'as' => 'password.reset',
    ]);
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
Route::get('password/reset/{token}', [ 
        'uses' => 'Auth\PasswordController@getReset',
        'as' => 'password.token',
    ]);
Route::post('password/reset', 'Auth\PasswordController@postReset');


/*
|--------------------------------------------------------------------------
| Support Pages Routes (and Controllers)
|--------------------------------------------------------------------------
|
| These four ruotes on one controller handle the static pages that could
| be showed, like the service terms of use, privacy and help.
|
*/

Route::get('contact', ['as' => 'contact', 'uses' => 'SupportPagesController@contact']);

Route::get('privacy', ['as' => 'privacy', 'uses' => 'SupportPagesController@privacy']);

Route::get('terms', ['as' => 'terms', 'uses' => 'SupportPagesController@terms']);

Route::get('help/import', ['as' => 'importhelp', 'uses' => 'SupportPagesController@importhelp']);
Route::get('help', ['as' => 'help', 'uses' => 'SupportPagesController@help']);


// Microsites routes

Route::get('site/{slug}', [ 
    'uses' => '\Klink\DmsMicrosites\Controllers\MicrositeController@show',
    'as' => 'microsites.slug',
]);

Route::resource('microsites', '\Klink\DmsMicrosites\Controllers\MicrositeController');
