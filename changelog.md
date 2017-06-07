# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/0.3.0/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added 

### Changed

### Fixed

### Removed

## [0.17.0] - 2017-06-07

### Added 

- New extensible `PreviewService` under `packages/contentprocessing`
- Experimental support for Spreadsheet preview
- Added PHPSpreadsheet dependency to version `dev-develop`

### Changed

- Moved `ThumbnailsService` from `workbench/dms-preview` to `packages/contentprocessing`
- Upgraded PHPWord dependency to version 0.13.0
- Upgraded PHPPresentation dependency to version 0.8.0
- Docker image now set the full `KLINK_DMS_APP_KEY` value instead the characters from 1 to 32.
  This is likely to break your builds as deployments usually have an application key that is 
  one character longer than needed. To fix this please upgrade your configured application 
  key to a 32 character string.

### Fixed

- Improved PowerPoint Presentation conversion to html
- "Publish" action from context menu do not appear if the user doesn't have the 
  capability for publishing documents

### Removed

- `workbench/dms-preview` package

## [0.16.0] - 2017-05-15

### Added 

- New sharing dialog with unshare option
- `/s/{publiclink}` to make a document with a public link previewable
- Showing document access information on the details panel
- Docker image building
 - image build from the content of the repository
 - configured apache to listen on port 80 instead of 443 with certificates
 - container exposes port 80
 - removed connection check to the K-Core. If the K-Core is not started the documents 
   section of the K-DMS will throw error
 - removed dependency on [empty-page](https://git.klink.asia/klinkdocumentation/empty-page)
- K-Box Contact details editing without requiring a connection to the K-Link Network

### Changed

- Preview page header logo now links to user's homeRoute (defined by `User::homeRoute()`)
- Now all the routes that requires to perform a login redirect to `/` instead of `/auth/login`
- KlinkApiController to support shared via public link
- DocumentsController@show redirects to preview (via KlinkApiController)
- Now dialogs can be opened if a panel is already visible
- User messages now supports Markdown
- Changed share token to use a SHA-256 hash instead of SHA-512

### Fixed

- Admin menu overlapping with page content
- Showing error on password reset page if email is not valid
- Redirect back to preview page after login in case login was required to view the preview. On
  IE or Edge this problem could arise again on development builds. If the problem persists execute 
  `php artisan config:cache`, this seems related to race conditions in the session handler 
  and some weird IE/Edge request processing, more info in Laravel issues [2962](https://github.com/laravel/framework/issues/2962),
  [8172](https://github.com/laravel/framework/issues/8172), [5416](https://github.com/laravel/framework/issues/5416) and [14385](https://github.com/laravel/framework/issues/14385)
- Added failure prevention in case a collection is a root, but it is not 
  connected to a project
- Showing error on password reset page if email is not valid
- Newlines support in User Messages
- Sharing list failure in case a shared document is in the trash
- Empty filter values are not considered while performing a search
- Fixed error handling when facet response is null
- Incorrect Email from address when sending messages directly to users using the Send Message feature

### Removed

- Language section. It was showing the language folders for UI localization. Totally 
  useless at this time
- Removed network card information from Network administration section
- `/home` route definition and deprecated the `HomeController`
- Removed copy document links from the details panel as now it is in the
  sharing dialog
- Map from contact page

## [0.15.4] - 2017-04-10

### Fixed

- (backport) Showing error on password reset page if email is not valid
- (backport) Newlines support in User Messages

### Changed

- (backport) User messages now supports Markdown

## [0.15.2] - 2017-03-14

### Fixed

- Backport of Option::mailFrom() method fix for ShareCreatedHandler

## [0.15.1] - 2017-03-09

### Changed

- Russian translation for starred and other menus

## [0.15.0] - 2017-03-02

### Added 

- `StorageService` for getting information on the storage status
- E-Mail driver specification in env file using `MAIL_DRIVER`. This enable to test email
  message sending without configuring a SMTP server. By default if the `log` driver is 
  configured the DMS let you create users and send emails. With the `log` driver all 
  email messages are written in the log files, accessible from Administration > Maintenance
- Share Email notification. An email is sent to the user who should receive the share.
- Share route. Now you can link to a share with id or token (shares/{token}) and the system
  redirects you to the collection or the document.
- Shared with me page: 
 - Possibility to order the list of shares according to the share creation date
 - The share creation date is now shown instead of the document/collection creation date
 - Empty state message
- Ability to set email configuration with environment variables in the `.env` file, see 
  `config/mail.php` for all the options.

### Changed

- Download button on details panel don't force the file download anymore (regression after)
  changing the behavior of the document URL to preview.
- Storage widget 
 - now do not require connection to a running K-Core
 - new UI
- User Session widget. Updated layout
- deprecated `DocumentsService::getStorageStatus` in favor of `StorageService`
- New admin menu layout, bigger icons, better icons and color response
- E-Mail configuration is now valid if all of the following settings are configured: `mail.host`, 
  `mail.port`, `mail.from.address`, `mail.from.name`. Therefore `Option::isMailEnabled` return false 
  if one of the previous setting (both from environment file or database) is not set. If the driver is
  set to `log` the mail configuration is considered valid and the SMTP server cannot be edited from the 
  UI.
- E-Mail configuration UI.
- Projects Page: clicking on a project now opens the project

### Fixed

- Javascript error in Projects page loading
- Localization `project.label.search_user_not_found` not available on Projects details panel
- URL cleaning from whitespace and anchor section before starting an import
- sharing a Document twice with the same user only creates one share record in the database.

### Removed

- Unused ViewComposers and views
- Usage of `mail.pretend` option to identify if the email configuration is valid


## [0.14.0] - 2017-02-13

### Changed

- Migration `2016_02_01_083232_update_import_error_handling.php` to use text column
  instead of json column because Laravel 5.2 mysql grammar uses a `json` column type, 
  which is not supported on MariaDB 10.1 and below. This should only affects newer
  deployments because database created with Laravel 5.1 uses the `text` column type.
- Migration `2016_04_06_083349_update_document_descriptors_with_last_error.php` to use text column
  instead of json column because Laravel 5.2 mysql grammar uses a `json` column type, 
  which is not supported on MariaDB 10.1 and below. This should only affects newer
  deployments because database created with Laravel 5.1 uses the `text` column type.
- Upgraded Laravel to version 5.2.*
- Upgraded K-Link Adapter Boilerplate to version 3.0.1 that fixes a bug in the thumbnail generation.
- Added information about invalid mail configuration to administrator dashboard and users
  page.
- Enabled drag and drop upload on recents, shared with me, trash and starred pages.

### Fixed

- Storage page loading error in case the reindex procedure is triggered 
  with no documents in the DMS.
- Enable document download if user is disabled, but document is in a project or shared.
- Regression in handling errors on the login form
- Regression in logout

## [0.13.3] - 2017-02-03

### Added 
- command `lang:check` to get the language strings not translated
- ThumbnailsService inside the Preview package for handling File 
  thumbnail generation.
- Outdated browser message for IE9 (and below) and Firefox 3.6
- Added `files:orphan` command to identify and remove orphan files.
- `KlinkAdapter` service refactoring with suppport for Mocks and Fake 
  instances to be used in unit tests

### Changed

- Thumbnail generation command (`thumbnail:generate`) uses the 
  added ThumbnailsService.
- Increased the size of the drag and drop sensible area to cover the all page.
- Better drag and drop hint message.
- K-Link prefixed routes
 - `klink/{id}/document` route now shows the preview
 - now the route support `download` and `preview` as parameters
 - no need to specify `?preview=true` to get the document preview, 
   on the `klink/{id}/document` route. The parameter is still supported
   for backward compatibility
- `share.share_link_section` string to "Share link"
- `share.document_link_copy` string to a much shorter version
- Copy document link from the details panel
 - The interaction that show success or error do not use dialog 
   windows anymore. It is now inline in the context of the button and the 
   error message is just below the address field.
 - The "copy document link" button now uses the preview link instead of 
   the file download link.

### Fixed

- Identification of an imported website that is a plain web page.
- Edge case that can generate an error while generating a preview 
  if the document is in a collection and accessed by a logged in 
  user

### Removed

- `DocumentsService::generateThumbnail`, use `ThumbnailsService`
- `DocumentsService::getDefaultThumbnail`, use `ThumbnailsService`

## [0.13.2] - 2016-12-22

### Fixed

- Regression in handling un-authenticated users that asks for preview or 
  download of public documents
- Regression in authorizing thumbnails and document preview for private
  documents

## [0.13.1] - 2016-12-21

### Added
- Russian translation for the Unified Search feature

### Fixed
- Prevent document download, preview and thumbnail to bew viewable by users 
  that do not have explicit access to the underlying document.
- Russian localization of cancel actions in user management and 
  institution management forms
- Public Network search box not shown if Network is disabled.
- Checking user preference on filters when invoked from the public search page
- Now a proper title is shown when you drag a collection over itself

## [0.13.0] - 2016-12-05

### Added
- Unified Search (under feature flag)
 - Projects page that lists all the projects with search across all of them.
 - Search ability to the recent page.
 - Ordering based on newest/oldest on recent page.
 - Change timeframe on the recent page.
 - Select the number of documents to show per page on the recent page.
 - Show, on the recent page, all updated documents in project/collection 
   I have access.
 - Ability to search users on the project details page.
- Ability to upload avatars for projects.
- Upgraded Adapter Boilerplate to 3.0.0-RC1.
- `dms:flags` command to enable/disable features that are in testing, 
  like the Unified Search (flag: unifiedsearch).

### Changed
- Personal collections are not included in collection filters when a project 
  collection is explored.
- The file already exists in Public network has a low priority.

### Fixed
- Max upload size configuration wrongly passed to the browser.
- Proper localization on the search page for guests users.
- server.php script for using `php artisan serve` command


## [0.12.5] - 2016-11-15

### Added

- Drag and drop over a collection on the sidebar starts the upload 
  in that collection. No more strange message is shown

### Changed

- better error message when upload size exceed the configured limit
- default file limit for upload is now exactly 200MB (was 198MB previously)

### Fixed

- Error while running the reindex command from Administration > Storage
- Drag and drop a folder in a project now creates collections under a project

## [0.12.3] - 2016-11-08

### Fixed

- wrong max file size limit was considered by the Drag and Drop upload
  handler


## [0.12.2] - 2016-11-07

### Changed

- UI: reduced the chance to get a multine action bar when language is set to russian
- UI: search box layout changes to improve cross browser compatibility

### Fixed

- reindex message is not visible anymore on the edit page of 
  a trashed document

## [0.12.1] - 2016-11-01

### Changed

- Better handling of the document hash when using K-Core version 2.2.1 on 
  unsupported files;
- enhancement: better wording on remove dialogs
- enhancement: better wording on the dialog that shows the result of a copy to collection

### Fixed

- clicking on the latest selected filter now return to default blank state,
  previously the results of the OR between all the collections in the filter were 
  returned
- handled the case when a File Already Exists message is triggered on a File 
  that do not have a Document Descriptor attached. Now a more clear message is returned
- details panel refresh error after document restore


## [0.12.0] - 2016-10-19

codename: [Aristotle Kristatos](http://jamesbond.wikia.com/wiki/Aris_Kristatos) from 
the James Bond film _For Your Eyes Only_


## Changed

- Enhancement to the file already exists message. Now the collection is clickable and the
  document in the collection is highlighted;
- The sidebar on the documents section now list collections in alphabetical order
- Collections filter improvements
 - The collection filters now shows the parent of a collection on mouse over
 - The project icon has been removed and the same color bars are used to 
   identify project and personal collections
 - Available collection filters in a collection are limited to 
   the current collection and its sub-collections. No more collections from other projects or personal
 - Finally fixed the possibility of selecting collections to be considered in AND, 
   i.e. to find documents that are both in two or more collections at the same time
 - No more locked (dark grey) collections in filters 



## [0.11.0] - 2016-10-06

codename: [Hugo Drax](https://en.wikipedia.org/wiki/Hugo_Drax) from the James Bond novel _Moonraker_

### Added

- Institution details page for institutions listed in the 
  `Administration > Institution` section

### Changed

- Enhancement to the file already exists message. Now it shows better 
  where the document is located so you don't waste time trying to search for it;
- Change: the storage widget on the administration page now takes into account 
  only private documents

### Fixed

- Now administrator are able to trash a collection in a project
- Import ownership check
- Loading administration section error if internet connection is absent 
  after configuring the K-Link Network connection
- Public document not updated when an already public document is edited


## [0.10.2] - 2016-09-21

codename: [Karl Stromberg](http://villains.wikia.com/wiki/Karl_Stromberg) from the James 
Bond novel _The Spy Who Loved Me_


## Changed

- Upgraded K-Link Adapter Boilerplate code to fix a silent issue affecting indexing of file 
  whose wise is between 100KB and 1MB
- Changed label "owned by institution" to "added by" according to users requests
- Better handling of error message visualization 
- Technical enhancement to the `dms:reindex` command
- Technical enhancement to the build pipeline
- Included technical requirements needed to support the K-Link Analytics platform

### Fixed

- Resolved bug that prevents the upgrade of Document Descriptor details when a new document 
  version is uploaded. This bug affected also the preview of the new document version



## [0.10.1] - 2016-07-20

codename: [Karl Stromberg](http://villains.wikia.com/wiki/Karl_Stromberg) from the James 
Bond novel _The Spy Who Loved Me_

### Added

- Proper date localization
- Alpha version of the Power Point file preview.

### Changed

- Project Edit page. Now that page is completly overhauled with a multiple select textbox 
  with autocomplete for selecting users and a new members list.
- The recent page now shows the last 1000 updated documents in a timeframe of 3 weeks. All 
  the parameters are configurable.
- For your personal documents now you can permanently delete if the DMS Administrator 
  enables the the permission on your profile. 
- Project Managers can permanently delete a document by default.
- The empty trash button will clean your trash entirely, no matter of the selection. If you
  want to permanently delete a document 
  use the right click menu. And by the way this is also valid for collections.
- Moving a collection from your personal to a project takes into account also the 
  sub-collections of the collection you are moving

### Fixed

- Administrators now properly see the project collections of a document in the details panel
- Network Name can be changed
- Rename of "Make public" button to "publish"


## [0.9.1] - 2016-06-15

codename: [Francisco Scaramanga](https://en.wikipedia.org/wiki/Francisco_Scaramanga) from the 
James Bond novel _The Man with the Golden Gun_

### Added

- Collections on the document edit page are now clickable and let you navigate to the collection
- Newly created collection is highlighted and showed
- Support widget on the error page
- Service Policy with information about copyrighted materials upload and code of conduct.
- Icons for Public and Private documents

### Changed 

- English and Russian version of the Frequently Asked Questions

### Fixed

- Fixed a situation that could cause to list twice the same document in the search result
- Fixed a case that could trigger a document unpublish from K-Link Network
- Fixed links in the help section
- Fixed a case when second or third level collections are not keep visible after navigation


## [0.8.0] - 2016-05-18

codename: [Dr. Kananga](https://en.wikipedia.org/wiki/Live_and_Let_Die_(film))

### Added

- The Help page now lists the Frequently Asked Questions about Search, Collections and getting started. The whole page 
is translated into russian. You will see the English or the russian version according to the language setting of your profile (if logged in the DMS), otherwise you will 
see the page in the preferred language specified from your browser.
- Localization of Javascript generated messages like context menu, dialogs and error messages
- Ability to retry and remove single imports
- Collection tree navigation
 - The current opened collection is put in the visible space, so you can see the selected collection when you enter it
 - When a collection is opened the list of childs will be automatically expanded


### Fixed

- We put the same hint for the _Starred_ and the _Recent_ section, now the correct one is showed
- We fixed the document language not correctly showed on K-Link Public searches in the tiles and details view
- Faster thumbnail process and some new iconography for emails and zip archive


## [0.7.0] - 2016-04-20

### Added

- `dms:import` CLI command
- `users:import` CLI command

### Changed 

- Video files and compression handling.
- Localization enhancements. We continue to improve Russian localization of the entire UI..

### Fixed

- Fixed a case when you are not able to remove the document abstract.
- Fixed a case that could prevent the _Shared with me_ page to load properly.
- Fixed a case when a user could not enter a collection even if is listed in the collections tree.
- Fixed an error that prevented the _Share creation_ dialog to show when user has language set to Russian.
- Seems that if a user don't have any personal collection and makes a search from the personal section an error is triggered, now is fixed.
- Fixed a case were the Microsite content is rendered wrongly if the default language is not set to english.
- Fixed a case that could leas to a public document being unpublished when editing.
- Finally we fixed the Contact page rendering issue.



## [0.6.1] - 2016-03-16

codename: [Emilio Largo](https://en.wikipedia.org/wiki/Emilio_Largo)

### Added

- Settings for the support key

### Fixed

- Localization enhancements. 
 - We resolved a couple of regressions in the localization and added more translations
 - Also the Support widget now inherits the language choice of the current user
 - You will see the Institution name instead of an ugly upper case string
- We reworked the search form and we added a couple of hint to it, hope you will find it good
- Enhanced the Document details panel to better show the collection in which a document has been added
- Somehow we missed to show the pagination in the shared with me page if you have more than 12 shared items, now it's there
- We've enhanced the filters visualization to prevent those horrible problems when long collection names are used
- A user must be affiliated to an institution before it can create a Microsite, now we will tell him
- Prevented to have a false sense of editability of a Public Document when press Edit 
- Now the document preview should correctly show the last version of a document
- We added a little bit of smartness to the collections listings (both under a Project or Personal)

### Fixed

- Fixed a bug that prevented the microsite content to be showed while editing
- We fixed also minor things only Project Admins and Administrators will see

### Changed

- In the collection filters a gray background with a message stating that the collection is locked has been added to highlight that the collection is the current opened one, this will happen only if you have opened a collection (no matter if under a project or personal)
- When the "make button" is clicked it now requires to have at least one document selected, to "make public" an entire collection please use the right click menu on the collection
- Now when a document is already added to a collection we will tell you instead of adding it twice
- The Support Widget will be in the disabled state after the upgrade, you have to contact the K-Link Team to obtain a valid support key to be inserted in Administration > Settings page



## [0.6.0] - 2016-02-17

codename: [Emilio Largo](https://en.wikipedia.org/wiki/Emilio_Largo)

### Added

- Microsite for each Project

### Changed

- Various Import from URL Enhancements and failure handling
- Import page compatibility on IE11 and below
- Increase max upload size to 200MB
- Upgraded the base framework to a Long-Term Supported version
- Due to changes in the security stack, on existing instances you have to change the `APP_KEY` parameter in 
  the `.env` configuration file. The `APP_KEY` must be exactly 16 characters long and must be a substring 
  from character 2 to 17 of the currently used key, otherwise every user must apply for a password reset.


## [0.5.8] - 2016-01-20

codename: [Auric Goldfinger](https://en.wikipedia.org/wiki/Auric_Goldfinger)

### Added

- Publish documents on K-Link Public
- You can finally specify a two words user nicename (feature suggested with a support ticket)
- Special characters in password support - _ ? ! + % & but not whitespace. You can now use some more characters in your password. (1)
- [Copy link button](http://klink.uservoice.com/forums/303582-k-link-dms/suggestions/10319055-copy-link-button)
- From this version you can also use the `dms:import` command line utility to import documents stored in the DMS storage folder.

### Fixed

- Some welcomed additions to the translation in the filtering portion of the UI and some typos cleaning here and there
- K-Link url path for document and thumbnail are wrongly protected
- Filters on collections are now highlight properly
- Document download filename sometimes is reported as download.pdf instead of the real document title
- The Reset account pages don't show an error while loading up


## [0.5.7] - 2016-12-18

codename: [Ernst Stavro Blofeld](https://en.wikipedia.org/wiki/Ernst_Stavro_Blofeld)

### Changed

- Upgraded to the latest K-Link Adapter Boilerplate to prepare the support for K-Link Public connection

### Fixed

- Project details page can be seen without Project Administration rights
- Public search can potentially leak private search results if no public documents are found for a particular search query


## [0.5.6] - 2015-11-11

codename: [Rosa Klebb](https://en.wikipedia.org/wiki/Rosa_Klebb)

### Added

- Added Search in shared, personal collections, projects
- Filtering are available also in shared, personal collections, projects
- Search in collection is performed in the current collection and all sub-collections
- Language switcher. Change the language of the UI based on user profile preferences
- Added the ability for admin users to change the user email addresses

### Fixed

- random empty page result
- search box on document edit page will perform search on document (personal or private depending on the user account)
- share dialog user list scroll
- disabled edit and moving action on Project roots
- added indexing retry with basic search functionality in case of Core Internal Error when indexing a supported file
- accounts administration - prevent editing the account you are logged in with
- prevent search box hiding when on low resolution screens
- wrong collection list in document edit page and details panel


## [0.5.5] - 2015-10-09

codename [Dr. Julius No](https://en.wikipedia.org/wiki/Julius_No)

### Added

- Support for Google Earth Files (KML and KMZ format);
- Support for plain text files: TXT, Markdown. For this file the preview is available;
- Support for Rich Text Format (RTF) files;
- Google Docs, Spreadsheets and Slides can also be uploaded and previewed;
- Ability to upload unsupported file types (only the file name will be used in case of search);
- Increased Russian translated UI elements;
- List style visualization persistence across page changes for the same user;
- Delete documents from the recent list;
- Ability to enable/disable the map visualization;

### Changed

- The maximum supported file size is now 100 MB;

### Fixed

- Better trash handling;
- Increased compatibility with Firefox and older versions of Internet Explorer.
- Increased contrast in the document details panel;

## [0.5.4] - 2015-09-18

### Changed

- Recent page now shows recent activities based on user profile. Regular users will see only personal activity listed, while the administrator will see updates on all the documents inside the DMS.

### Fixed

- Pagination: it has been a while that the pagination links at the bottom of each pages report a different number of pages than the reality. Now this has been fixed ;)
- The footer text sometimes was floating around obscuring the documents list, now is back in it's place at the bottom of the page


## [0.5.3] - 2015-08-10

### Added

- Alpha version of the Russian localization
- A more contextual "Create Collection": The Create collection button is back and is context sensitive. The collection will be created with the correct visibility right out the box. When created from inside a Project the parent collection will be setup for you.
- Collection move with drag and drop: You can now organize collections by simply moving them around with the drag and drop.
- Recognize the K-Link DMS among all your opened tabs with the favicon.

### Changed

- A real drag and drop area for file upload: We've improved the sensitive area for the drag and drop based file upload and, also, we have added a little hint message to make you aware of the action.
- better upload error reports
- document indexing fix when the document language is unknown
- restored email validation when creating users
- better error messages
- delete documents enhancements
