<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register view routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "web" middleware group.
*/

/*
|--------------------------------------------------------------------------
| Main Access page
|--------------------------------------------------------------------------
*/
Route::get('/', ['as' => 'frontpage', 'uses' => 'WelcomeController@index']);

/*
|--------------------------------------------------------------------------
| Public/Network search
|--------------------------------------------------------------------------
|
| ...
|
*/
Route::get('search/autocomplete', ['as' => 'search-autocomplete', 'uses' => 'SearchController@autocomplete']);
Route::get('search', ['as' => 'search', 'uses' => 'SearchController@index']);

/*
|--------------------------------------------------------------------------
| Administration
|--------------------------------------------------------------------------
|
| ...
|
*/
Route::group(['as' => 'administration.', 'prefix' => 'administration'], function () {
    Route::get('/', [
        'as' => 'index',
        'uses' => 'Administration\AdministrationDashboardController@index',
        'permission' => KBox\Capability::MANAGE_DMS]);

    Route::get('users/{id}/remove', [
    'as' => 'users.remove',
    'uses' => 'Administration\UserAdministrationController@remove',
    'permission' => KBox\Capability::MANAGE_USERS]);

    Route::get('users/{id}/restore', [
    'as' => 'users.restore',
    'uses' => 'Administration\UserAdministrationController@restore',
    'permission' => KBox\Capability::MANAGE_USERS]);
    
    Route::get('users/{id}/resetpsw', [
    'as' => 'users.resetpassword',
    'uses' => 'Administration\UserAdministrationController@resetPassword',
    'permission' => KBox\Capability::MANAGE_USERS]);

    Route::resource('/users', 'Administration\UserAdministrationController');

    Route::resource('/messages', 'Administration\MessagingController', ['only' => ['create', 'store']]);

    Route::resource('/institutions', 'Administration\InstitutionsController', ['only' => ['index', 'show']]);

    Route::get('/network', 'Administration\NetworkAdministrationController@getIndex')->name('network.index');

    Route::get('/storage', 'Administration\StorageAdministrationController@getIndex')->name('storage.index');
    Route::get('/storage/reindexall', 'Administration\StorageAdministrationController@getReindexAll')->name('storage.reindexstatus');
    Route::post('/storage/reindexall', 'Administration\StorageAdministrationController@postReindexAll')->name('storage.reindexall');
    Route::post('/storage/naming', 'Administration\StorageAdministrationController@postNaming')->name('storage.naming');

    Route::get('/maintenance', 'Administration\MaintenanceAdministrationController@getIndex')->name('maintenance.index');

    Route::get('/mail', 'Administration\MailAdministrationController@getIndex')->name('mail.index');
    Route::post('/mail', 'Administration\MailAdministrationController@postStore')->name('mail.store');
    Route::get('/mail/test', 'Administration\MailAdministrationController@getTest')->name('mail.test');

    Route::resource('/settings', 'Administration\SettingsAdministrationController', ['only' => ['index', 'store']]);

    Route::resource('/identity', 'Administration\IdentityController', ['only' => ['index', 'store']]);
    
    // document licenses administration
    Route::get('/licenses', 'Administration\DocumentLicenses\DocumentLicensesController@index')->name('licenses.index');
    Route::put('/licenses/default', 'Administration\DocumentLicenses\DefaultDocumentLicensesController@update')->name('licenses.default.update');
    Route::put('/licenses/available', 'Administration\DocumentLicenses\AvailableDocumentLicensesController@update')->name('licenses.available.update');

    // Plugins related routes
    Route::resource('/plugins', 'Plugins\PluginsController', ['only' => ['index'/*, 'show', 'edit'*/, 'update', 'destroy']]);
});
/*
|--------------------------------------------------------------------------
| Documents and collections
|--------------------------------------------------------------------------
|
| ...
|
*/

// publish and unpublish routes

Route::post('/published-documents', [
    'uses' => 'PublishedDocumentsController@store',
    'as' => 'documents.publish',
]);

Route::delete('/published-documents/{id}', [
    'uses' => 'PublishedDocumentsController@destroy',
    'as' => 'documents.unpublish',
]);

Route::get('/d/download/{uuid}/{versionUuid?}', [
    'uses' => 'Document\DocumentDownloadController@show',
    'as' => 'documents.download',
]);

Route::get('/d/show/{uuid}/{versionUuid?}', [
    'uses' => 'Document\DocumentPreviewController@show',
    'as' => 'documents.preview',
]);

Route::get('/d/thumbnail/{uuid}/{versionUuid?}', [
    'uses' => 'Document\DocumentThumbnailController@show',
    'as' => 'documents.thumbnail',
]);

Route::group(['as' => 'documents.', 'prefix' => 'documents'], function () {
    Route::resource('groups', 'Document\GroupsController');

    Route::get('/recent/{range?}', [
            'uses' => 'Document\RecentDocumentsController@index',
            'as' => 'recent',
        ])->where(['range' => 'today|yesterday|currentweek|currentmonth']);

    Route::get('/trash', [
            'uses' => 'Document\DocumentsController@trash',
            'as' => 'trash',
        ]);
    Route::delete('/trash', [
        'uses' => 'Document\BulkController@emptytrash',
        'as' => 'bulk.emptytrash',
    ]);
    Route::get('/notindexed', [
            'uses' => 'Document\DocumentsController@notIndexed',
            'as' => 'notindexed',
        ]);

    Route::get('/shared-with-me', [
            'uses' => 'Document\DocumentsController@sharedWithMe',
            'as' => 'sharedwithme',
        ]);

    Route::resource(
        '/starred',
        'Document\StarredDocumentsController',
        ['only' => ['index', 'show', 'store', 'destroy']]
    );

    Route::resource(
        '/projects',
        'Projects\ProjectsPageController',
        ['only' => ['index', 'show']]
    );

    Route::get('/{institution}/{local_id}', [
            'uses' => 'Document\DocumentsController@showByKlinkId',
            'as' => 'by-klink-id',
        ])->where(['local_id' => '(?!edit)[A-Za-z0-9]+', 'institution' => '[A-Za-z0-9]+']);

    Route::get('/{visibility}', [
            'uses' => 'Document\DocumentsController@index',
            'as' => 'visibility',
        ])->where(['visibility' => '(public|private|personal)']);
    
    Route::get('/public/{uuid}', [
            'uses' => 'NetworkDocumentsController@show',
            'as' => 'network.show',
        ]);

    Route::put('/restore', [
        'uses' => 'Document\BulkController@restore',
        'as' => 'bulk.restore',
    ]);

    Route::post('/remove', [
        'uses' => 'Document\BulkController@destroy',
        'as' => 'bulk.remove',
    ]);
    
    Route::post('/makepublic', [
        'uses' => 'Document\BulkController@makePublic',
        'as' => 'bulk.makepublic',
    ]);

    Route::post('/makeprivate', [
        'uses' => 'Document\BulkController@makePrivate',
        'as' => 'bulk.makeprivate',
    ]);

    Route::post('/copy', [
        'uses' => 'Document\BulkController@copyTo',
        'as' => 'bulk.copyto',
    ]);
});

Route::get('/documents/{document}/versions/{version}', [
    'uses' => 'Document\DocumentVersionsController@show',
    'as' => 'documents.version.show'
]);

Route::put('/documents/{document}/versions/{version}/restore', [
    'uses' => 'Document\RestoreVersionsController@update',
    'as' => 'documents.version.restore'
]);

Route::delete('/documents/{document}/versions/{version}', [
    'uses' => 'Document\DocumentVersionsController@destroy',
    'as' => 'documents.version.destroy'
]);

Route::resource('/documents', 'Document\DocumentsController');

/*
|--------------------------------------------------------------------------
| Duplicate Documents
|--------------------------------------------------------------------------
*/

Route::delete('/duplicate-documents/{id}', [
    'uses' => 'Document\DuplicateDocumentsController@destroy',
    'as' => 'duplicates.destroy'
]);

/*
|--------------------------------------------------------------------------
| Sharing
|--------------------------------------------------------------------------
|
| ...
|
*/

Route::put('shares/deletemultiple', [
    'uses' => 'SharingController@deleteMultiple',
    'as' => 'shares.deletemultiple',
  ]);

Route::get('shares/group/{id}', [
        'uses' => 'SharingController@showGroup',
        'as' => 'shares.group',
    ]);

Route::resource('shares', 'SharingController');

// Public links creation and management
// is an extension of sharing with a new target type

Route::resource('links', 'PublicLinksController', [
    'except' =>['index', 'create', 'edit']]);

Route::get('s/{link}', [
    'as' => 'publiclinks.show',
    'uses' => 'PublicLinksShowController@show'
    ])->where([
        'link' => '[0-9a-zA-Z\\-]+'
    ]);

/*
|--------------------------------------------------------------------------
| User Profile
|--------------------------------------------------------------------------
|
| Handle the user profile
|
*/

// used to store document layout and other options via async requests
Route::post('profile/options', [
      'uses' => 'UserProfileController@update',
      'as' => 'profile.update',
]);

// the profile page
Route::resource('profile', 'UserProfileController', ['only' => ['index', 'store']]);

/*
|--------------------------------------------------------------------------
| User/People groups
|--------------------------------------------------------------------------
|
| Handle the (people) groups functionality
|
*/

Route::resource('people', 'People\PeopleGroupsController');

/*
|--------------------------------------------------------------------------
| Projects
|--------------------------------------------------------------------------
|
| Handle the microsites visualization and edit
|
*/

\Route::get('projects/{slug}/{language?}', [
    'uses' => '\Klink\DmsMicrosites\Controllers\MicrositeController@show',
    'as' => 'projects.site',
])->where([
    'slug' => '(?!create)[a-z\\-]+',
    // slug cannot contain 'create' as generates a conflict with projects.create route
     'language' => '^[a-z]{2}$'
]);

Route::post('projects/{id}/avatar', [
    'uses' => 'Projects\ProjectAvatarsController@store',
    'as' => 'projects.avatar.store',
]);
Route::get('projects/{id}/avatar', [
    'uses' => 'Projects\ProjectAvatarsController@index',
    'as' => 'projects.avatar.index',
]);
Route::delete('projects/{id}/avatar', [
    'uses' => 'Projects\ProjectAvatarsController@destroy',
    'as' => 'projects.avatar.destroy',
]);
Route::resource('projects', 'Projects\ProjectsController');

/*
|--------------------------------------------------------------------------
| External Accessible Routes
|--------------------------------------------------------------------------
|
| External route for get the document content and thumbnail
|
*/

Route::get('klink/{id}/{action}/{version?}', [
        'uses' => 'KlinkApiController@show',
        'as' => 'klink_api',
    ])->where(['id' => '[0-9A-Za-z]+', 'action' => '(thumbnail|document|preview|download)']);

// Direct download of any file, given its uuid
Route::get('files/{uuid}', [
    'uses' => 'FileDownloadController@show',
    'as' => 'files.download',
]);

// video streaming, handle manifest files and range request for video resources
Route::get('stream/{uuid}/{resource?}', [
    'uses' => 'VideoPlaybackController@show',
    'as' => 'video.play',
])->where(['resource' => 'mpd|.*\-(\d*)\_(audio|video)\.mp4']);

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

Auth::routes();

/*
|--------------------------------------------------------------------------
| Support Pages Routes (and Controllers)
|--------------------------------------------------------------------------
|
| These handle the static pages that could be showed,
| like the service terms of use, privacy and help.
|
*/

Route::get('contact', ['as' => 'contact', 'uses' => 'ContactPageController@index']);

Route::get('privacy', ['as' => 'privacy', 'uses' => 'SupportPagesController@privacy']);

Route::get('terms', ['as' => 'terms', 'uses' => 'SupportPagesController@terms']);

Route::get('help', ['as' => 'help', 'uses' => 'SupportPagesController@help']);

Route::get('help/browserupdate', ['as' => 'browserupdate', 'uses' => 'SupportPagesController@browserupdate']);

Route::get('help/licenses', ['as' => 'help.licenses', 'uses' => 'LicensesHelpController@index']);

/*
|--------------------------------------------------------------------------
| Microsites
|--------------------------------------------------------------------------
|
| Handle the microsites visualization and edit
|
*/

Route::get('site/{slug}', [
    'uses' => '\Klink\DmsMicrosites\Controllers\MicrositeController@show',
    'as' => 'microsites.slug',
]);

Route::resource('microsites', '\Klink\DmsMicrosites\Controllers\MicrositeController');

/*
|--------------------------------------------------------------------------
| Upload related routes
|--------------------------------------------------------------------------
|
| The new upload mechanism
|
*/

Route::get('/uploads', [
    'uses' => 'UploadPageController@index',
    'as' => 'uploads.index',
]);

Route::post('/uploadjobs', 'UploadJobsController@store');
Route::get('/uploadjobs/{id}', 'UploadJobsController@show');
Route::delete('/uploadjobs/{id}', 'UploadJobsController@destroy');

/*
|--------------------------------------------------------------------------
| Old /dms routes redirect
|--------------------------------------------------------------------------
|
| Redirect the get requests to the old /dms path to the root
|
*/

Route::prefix('dms')->group(function () {
    Route::get('/', 'DmsRoutesController@index');

    Route::get('/{route}', 'DmsRoutesController@show')->where('route', '[A-Za-z0-9\-\_\/]+');
});
