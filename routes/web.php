<?php

use Illuminate\Support\Facades\Route;

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
        'permission' => KBox\Capability::MANAGE_KBOX]);

    Route::get('users/{id}/restore', [
    'as' => 'users.restore',
    'uses' => 'Administration\UserAdministrationController@restore']);
    
    Route::get('users/{id}/resetpsw', [
    'as' => 'users.resetpassword',
    'uses' => 'Administration\UserAdministrationController@resetPassword']);

    Route::resource('/users', 'Administration\UserAdministrationController');

    Route::resource('/messages', 'Administration\MessagingController', ['only' => ['create', 'store']]);

    Route::get('/network', 'Administration\NetworkAdministrationController@getIndex')->name('network.index');

    Route::get('/storage', 'Administration\StorageAdministrationController@getIndex')->name('storage.index');
    Route::get('/storage/reindexall', 'Administration\StorageAdministrationController@getReindexAll')->name('storage.reindexstatus');
    Route::post('/storage/reindexall', 'Administration\StorageAdministrationController@postReindexAll')->name('storage.reindexall');
    Route::post('/storage/naming', 'Administration\StorageAdministrationController@postNaming')->name('storage.naming');
    
    Route::get('/storage/files', 'Administration\AllFilesController@index')->name('storage.files');

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

    Route::resource('/plugins', 'Plugins\PluginsController', ['only' => ['index'/*, 'show', 'edit'*/, 'update', 'destroy']]);

    Route::get('/analytics', 'Administration\AnalyticsController@index')->name('analytics.index');
    Route::put('/analytics', 'Administration\AnalyticsController@update')->name('analytics.update');
    
    Route::get('/support', 'Administration\SupportController@index')->name('support.index');
    Route::put('/support', 'Administration\SupportController@update')->name('support.update');
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

// Embed route to be used by the OEmbed feature
Route::get('/d/embed/{uuid}/{versionUuid?}', [
    'uses' => 'Document\DocumentEmbedController@show',
    'as' => 'documents.embed',
]);

Route::get('/groups/{group}/details', [
    'uses' => 'GroupDetailsController@show',
    'as' => 'groups.detail',
]);

Route::group(['as' => 'documents.', 'prefix' => 'documents'], function () {
    Route::resource(
        'groups',
        'Document\GroupsController',
        ['exclude' => ['index']]
    );

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
            'uses' => 'RedirectOldDocumentsController@show',
            'as' => 'by-klink-id',
        ])->where(['local_id' => '(?!edit)[A-Za-z0-9]+', 'institution' => '[A-Za-z0-9]+']);

    Route::get('/public', [
            'uses' => 'Document\PublicDocumentsController@index',
            'as' => 'public_visibility',
        ]);

    Route::get('/{visibility}', [
            'uses' => 'Document\DocumentsController@index',
            'as' => 'visibility',
        ])->where(['visibility' => '(private|personal)']);
    
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

Route::get('shares/group/{group}', [
        'uses' => 'SharingController@showGroup',
        'as' => 'shares.group',
    ]);

Route::post('shares/find-targets', [
    'uses' => 'FindSharingTargetsController@index',
    'as' => 'shares.targets.find',
]);

Route::get('shares/list-users', [
    'uses' => 'ListUsersWithAccess@index',
    'as' => 'shares.users',
]);

Route::resource('shares', 'SharingController');

// Public links creation and management
// is an extension of sharing with a new target type

Route::post('links', 'PublicLinksController@store')->name("links.store");
Route::put('links/{id}', 'PublicLinksController@update')->name("links.update");
Route::delete('links/{id?}', 'PublicLinksController@destroy')->name("links.destroy");

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

Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', 'UserProfileController@index')->name('index');
    Route::put('/', 'UserProfileController@update')->name('update');

    Route::get('privacy', 'UserPrivacyController@index')->name('privacy.index');
    Route::put('privacy', 'UserPrivacyController@update')->name('privacy.update');
    Route::get('password', 'UserPasswordController@index')->name('password.index');
    Route::put('password', 'UserPasswordController@update')->name('password.update');
    Route::get('email', 'UserEmailController@index')->name('email.index');
    Route::put('email', 'UserEmailController@update')->name('email.update');
    Route::put('language', 'ChangeUserLanguagePreferenceController')->name("language.update");
    
    // used to store document layout and other options via async requests
    Route::put('options', 'UserOptionsController@update')->name('options.update');

    Route::get('data-export', 'PersonalExportController@index')->name('data-export.index');
    Route::post('data-export', 'PersonalExportController@store')->name('data-export.store');
    Route::get('data-export/{export}', 'PersonalExportController@show')->name('data-export.download');

    Route::get('storage', 'Profile\UserQuotaController@index')->name('storage.index');
    Route::put('storage', 'Profile\UserQuotaController@update')->name('storage.update');

    Route::resource('/invite', 'Profile\InvitesController', ['only' => ['index', 'create', 'store', 'destroy']]);
});

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
Route::resource('projects', 'Projects\ProjectsController', ['except' => 'destroy']);

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

Auth::routes([
    'verify' => true,
    'register' => \KBox\Auth\Registration::isEnabled(), // config('registration.enable')
]);

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

Route::get('privacy/legal', ['as' => 'privacy.legal', 'uses' => 'Pages\PrivacyLegalPageController@index']);

Route::get('privacy', ['as' => 'privacy.summary', 'uses' => 'Pages\PrivacySummaryPageController@index']);

Route::get('terms', ['as' => 'terms', 'uses' => 'Pages\TermsPageController@index']);

Route::get('help', ['as' => 'help', 'uses' => 'SupportPagesController@help']);

Route::get('help/browserupdate', ['as' => 'browserupdate', 'uses' => 'SupportPagesController@browserupdate']);

Route::get('help/licenses', ['as' => 'help.licenses', 'uses' => 'LicensesHelpController@index']);

/*
|--------------------------------------------------------------------------
| Consent routes
|--------------------------------------------------------------------------
|
| Handle consent dialog requests and management.
|
*/
Route::prefix('consent')->name('consent.dialog.')->group(function () {
    Route::get('privacy', ['as' => 'privacy.show', 'uses' => 'PrivacyConsentDialogController@show']);
    Route::put('privacy', ['as' => 'privacy.update', 'uses' => 'PrivacyConsentDialogController@update']);
    Route::get('notification', ['as' => 'notification.show', 'uses' => 'NotificationConsentDialogController@show']);
    Route::put('notification', ['as' => 'notification.update', 'uses' => 'NotificationConsentDialogController@update']);
    Route::get('statistic', ['as' => 'statistic.show', 'uses' => 'StatisticConsentDialogController@show']);
    Route::put('statistic', ['as' => 'statistic.update', 'uses' => 'StatisticConsentDialogController@update']);
});

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

\Oneofftech\Identities\Facades\Identity::routes();
