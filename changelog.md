# Changelog

Lists all notable changes to the K-Box for each version.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

**Note**: Changes for the unreleased section are handled via entries under 
[changelogs/unreleased](./changelogs/unreleased). Please see the 
[developer documentation](./docs/developer/changelog.md) for 
instructions on adding your own entry.

## [Unreleased]

## [0.32] - 2020-11-28

### Added

- Log in and registration via external identity providers (e.g. Gitlab and Dropbox)  ([#422](https://github.com/k-box/k-box/issues/422), [#437](https://github.com/k-box/k-box/pull/437))

### Changed

- Allow project creation from the sidebar  ([#460](https://github.com/k-box/k-box/pull/460))

### Deprecated

- Support for Internet Explorer 11 and below
- Video uploader using resumable protocol

## [0.31.6] - 2020-10-15

### Fixed

- Fix error when exporting projects named with slash or containing as collections or documents ([#451](https://github.com/k-box/k-box/pull/451))
- Fix duplicate files included into project export ([#451](https://github.com/k-box/k-box/pull/451))

## [0.31.5] - 2020-10-14

### Added

- Project export command ([#448](https://github.com/k-box/k-box/pull/448))


## [0.31.4] - 2020-10-13

### Changed

- Update translation for Russian and Tajik languages by @najiba-f ([#446](https://github.com/k-box/k-box/pull/446))

### Fixed

- Improve accessibility check when sharing documents and collections by @AlbinaMuzafarova ([#443](https://github.com/k-box/k-box/pull/443))
- Fix retrieve of number of documents per page when user not logged in ([#447](https://github.com/k-box/k-box/pull/447))
- Fix markdown preview hide parts of a document ([#445](https://github.com/k-box/k-box/pull/445))

## [0.31.3] - 2020-09-22

### Added

- Publication retry command to retry failed publications on K-Link ([#441](https://github.com/k-box/k-box/pull/441))

### Fixed

- Prevent failed K-Link publication to cause export error ([#440](https://github.com/k-box/k-box/pull/440))
- Create Collection button creates sub-collections when browsing a collection by @AlbinaMuzafarova ([#439](https://github.com/k-box/k-box/pull/439)) (regression)

## [0.31.2] - 2020-09-21

### Added

- Export documents published on K-Link as compressed zip archive ([#438](https://github.com/k-box/k-box/pull/438))

## [0.31.1] - 2020-09-11

### Added

- Select the number of documents to show per page by @najiba-f ([#431](https://github.com/k-box/k-box/pull/431))

## [0.31.0] - 2020-09-02

### Added

- Appearance configuration to change login picture and color ([#419](https://github.com/k-box/k-box/pull/419))
- Various components for copy to clipboard, submitting forms via ajax, implementing a select2 dialog ([#412](https://github.com/k-box/k-box/pull/412))
- New layout for error pages ([#427](https://github.com/k-box/k-box/pull/427))
- Automate release to Docker Hub via GitHub Actions ([#428](https://github.com/k-box/k-box/pull/428), [#429](https://github.com/k-box/k-box/pull/429))

### Changed

- Create collection dialog, introduce new mobile friendly design ([#412](https://github.com/k-box/k-box/pull/412))
- Sharing dialog introduce new mobile friendly design and improve reactivity when sharing or unsharing ([#412](https://github.com/k-box/k-box/pull/412))
- Refactor user management authorization via policies ([#414](https://github.com/k-box/k-box/pull/414))

### Fixed

- `artisan route:list` command not working due to use of abort helper ([#416](https://github.com/k-box/k-box/pull/416))
- Authorization check for document editing ([#425](https://github.com/k-box/k-box/pull/425))
- Margins on static pages (e.g. help) when viewed on small screens ([#434](https://github.com/k-box/k-box/pull/434))

## [0.30.0] - 2020-08-08

### Added

- Automated test execution on PHP 7.4 ([#410](https://github.com/k-box/k-box/issues/410))

### Changed

- Update Laravel to version 7 ([#391](https://github.com/k-box/k-box/issues/391), [#395](https://github.com/k-box/k-box/pull/395), [#392](https://github.com/k-box/k-box/pull/392))
- Replaced Gulp with Laravel Mix and Webpack ([#386](https://github.com/k-box/k-box/issues/386), [#387](https://github.com/k-box/k-box/pull/387))
- Dropdown menus are now powered by AlpineJs ([#406](https://github.com/k-box/k-box/issues/406)
- Refactor projects authorization handling using policies ([#390](https://github.com/k-box/k-box/pull/390)
- Modernize login screen ([#408](https://github.com/k-box/k-box/pull/408), [#396](https://github.com/k-box/k-box/issues/396))
- Refactor mail settings page ([#401](https://github.com/k-box/k-box/pull/401)
- Update video processing cli to version 0.6.0 ([#433](https://github.com/k-box/k-box/pull/433))

### Fixed

- PDF read/write disabled in Image Magick on latest Docker builds ([#403](https://github.com/k-box/k-box/issues/403), [#404](https://github.com/k-box/k-box/pull/404))
- Mail settings page loading when log driver is used (after Laravel 7 upgrade) ([#399](https://github.com/k-box/k-box/pull/399))
- Upload drop hint appear when moving documents already uploaded ([#306](https://github.com/k-box/k-box/issues/306), [#400](https://github.com/k-box/k-box/pull/400))

### Deprecated

- PHP 7.2 support is deprecated. Future versions will require PHP 7.4 ([#413](https://github.com/k-box/k-box/issues/413))

### Removed

- Removed institutions tables and relationships from database ([#339](https://github.com/k-box/k-box/issues/339), [#405](https://github.com/k-box/k-box/pull/405))
- Removed `dom-crawler` and `css-selector` from direct dependency ([#393](https://github.com/k-box/k-box/pull/393))


## [0.29.1] - 2020-06-19

### Added

- Internal events for documents, projects and collections ([#378](https://github.com/k-box/k-box/pull/378))

### Changed

- Completed the switch to TailwindCSS and improve of layouts ([#383](https://github.com/k-box/k-box/pull/383), [#385](https://github.com/k-box/k-box/pull/385))
- Username in account listing is now a link to the edit page ([#376](https://github.com/k-box/k-box/pull/376))
- Remove deprecated string helpers ([#381](https://github.com/k-box/k-box/pull/381))

## [0.29.0] - 2019-12-09

### Added

- Audio file (mp3) preview ([#285](https://github.com/k-box/k-box/pull/285))
- User storage quota ([#286](https://github.com/k-box/k-box/pull/286), [#297](https://github.com/k-box/k-box/pull/297), [#326](https://github.com/k-box/k-box/pull/326))
- UUID generation for users, collections and projects ([#298](https://github.com/k-box/k-box/pull/298), [#300](https://github.com/k-box/k-box/pull/300), [#302](https://github.com/k-box/k-box/pull/302))
- User invitation ([#313](https://github.com/k-box/k-box/pull/313), [#327](https://github.com/k-box/k-box/pull/327))
- Store the user that perform the application of a collection to a file ([#317](https://github.com/k-box/k-box/pull/317))
- Collection details panel ([#324](https://github.com/k-box/k-box/pull/324))
- View shared collections hierarchy on the sidebar ([#330](https://github.com/k-box/k-box/pull/330), [#334](https://github.com/k-box/k-box/pull/334))
- Support mail link ([#341](https://github.com/k-box/k-box/pull/341))

### Changed

- Overall layout structure, colors and initial move to utility-first for main styles ([issue#348](https://github.com/k-box/k-box/issues/348), [#278](https://github.com/k-box/k-box/pull/278), [#292](https://github.com/k-box/k-box/pull/292), [#293](https://github.com/k-box/k-box/pull/293), [#294](https://github.com/k-box/k-box/pull/294), [#295](https://github.com/k-box/k-box/pull/295), [#337](https://github.com/k-box/k-box/pull/337))
- Sharing dialog user search is now asynchronous and filter by name (or email) ([#309](https://github.com/k-box/k-box/issues/309), [#355](https://github.com/k-box/k-box/pull/355))
- Migrate to PHP 7.2 ([issue#303](https://github.com/k-box/k-box/issues/303), [#323](https://github.com/k-box/k-box/pull/323) _community contribution_, [#333](https://github.com/k-box/k-box/pull/333))
- Update Xpdf tools to version 4.02 ([#311](https://github.com/k-box/k-box/pull/311))
- Improve date and time output on the interface by showing the timezone ([#321](https://github.com/k-box/k-box/pull/321))

### Deprecated

- Project Microsite feature. The feature is now moved behind the `microsite` flag and not active by default. Will be removed in a next version. ([#289](https://github.com/k-box/k-box/pull/289))

### Removed

- Initial removal of institutions feature ([#340](https://github.com/k-box/k-box/pull/340))
- Laravel Browserkit dependency ([#351](https://github.com/k-box/k-box/pull/351))
- Usage of array helpers per Laravel deprecation policy ([#353](https://github.com/k-box/k-box/pull/353))

### Fixed

- Duplicate resolution in same collection causing multiple entries ([#287](https://github.com/k-box/k-box/pull/287))
- Improve project duplicate error message ([#288](https://github.com/k-box/k-box/pull/288))
- Properly show errors while creating public links ([#290](https://github.com/k-box/k-box/pull/290))
- Profile update denied if user did not change the nicename ([issue#308](https://github.com/k-box/k-box/issues/308), [#310](https://github.com/k-box/k-box/pull/310) _community contribution_)
- Regression that hide document listing from shared with me [#320](https://github.com/k-box/k-box/pull/320))
- Accept locale from browser request that is not supported [#322](https://github.com/k-box/k-box/pull/322))
- Current user interface language not correctly reported ([#335](https://github.com/k-box/k-box/pull/335))
- Show only existing pages in menu ([#336](https://github.com/k-box/k-box/pull/336))
- Regression in microsite create and edit layout ([#365](https://github.com/k-box/k-box/pull/365))
- Microsite creation and editing validation rules ([#366](https://github.com/k-box/k-box/pull/366))

### Security

- Disable projects sharing in favor of membership ([#356](https://github.com/k-box/k-box/issues/356), [#358](https://github.com/k-box/k-box/pull/358))


## [0.28.4] - 2019-09-05

### Added

- Perform opcache_reset when enabling/disabling plugins ([#284](https://github.com/k-box/k-box/pull/284))

### Fixed

- Improved validation of collection and project filters

### Security

- Mitigate un-authorized collection listing in sharing filters
- Mitigate un-authorized file listing when performing search in the projects section
- Mitigate un-authorized file listing when performing search in the starred section

## [0.28.3] - 2019-09-04

### Security

- Document owner name and file uploader disclosure to unauthorized users

## [0.28.2] - 2019-08-22

### Fixed

- Ensure maximum upload size is considered an integer ([#277](https://github.com/k-box/k-box/pull/277))
- Ensure unsupported browser page loads ([#279](https://github.com/k-box/k-box/pull/279))
- Fix use of undefined $publication variable ([#276](https://github.com/k-box/k-box/pull/276))

### Changed

- Remove deprecated local k-search connection command ([#281](https://github.com/k-box/k-box/pull/281))

## [0.28.1] - 2019-08-05

### Added

- Added Open Data Commons Open Database License ([#269](https://github.com/k-box/k-box/pull/269))

### Fixed

- Fixed register routes used as link to login page ([#274](https://github.com/k-box/k-box/pull/274))

### Changed

- Open links in new tab if in abstract or in any markdown rendered text ([#272](https://github.com/k-box/k-box/pull/272))


## [0.28.0] - 2019-06-26

### Added

- Setting analytics service using environment variables ([#266](https://github.com/k-box/k-box/pull/266))
- Documentation on how to restore backups ([#268](https://github.com/k-box/k-box/pull/268)) 
- Documentation on how to change K-Box domain ([#268](https://github.com/k-box/k-box/pull/268)) 
- Environment variables to enable/disable UserVoice support ([#267](https://github.com/k-box/k-box/pull/267)) 

### Changed

- Analytics configuration now has a separate page in the administration area ([#266](https://github.com/k-box/k-box/pull/266))
- Support configuration now has a separate page in the administration area ([#267](https://github.com/k-box/k-box/pull/267)) 

### Fixed

- Documentation revision ([#268](https://github.com/k-box/k-box/pull/268)) 

### Deprecated

- `KBOX_SUPPORT_TOKEN` environment variable, use `KBOX_SUPPORT_USERVOICE_TOKEN` instead ([#267](https://github.com/k-box/k-box/pull/267))

## [0.27.2] - 2019-05-31

### Added

- Personal data export ([#261](https://github.com/k-box/k-box/pull/261))

## [0.27.1] - 2019-05-31

### Added 

- Readonly mode ([#258](https://github.com/k-box/k-box/pull/258))

### Changed

- Update axios to version 0.19 ([#260](https://github.com/k-box/k-box/pull/260))

## [0.27.0] - 2019-05-13

### Added

- User registration. By default is disabled, to enable set the environment variable `KBOX_USER_REGISTRATION` to `true` ([#241](https://github.com/k-box/k-box/issues/241), [#245](https://github.com/k-box/k-box/pull/245))

### Changed

- Laravel upgrade to version 5.7 ([#239](https://github.com/k-box/k-box/pull/239))
- Changed the logo to respect the K-Box branding ([#242](https://github.com/k-box/k-box/pull/242))
- Improve iconography when no documents or share can be found ([#252](https://github.com/k-box/k-box/pull/252))
- Explicit validation of required capabilities when creating a new account ([#253](https://github.com/k-box/k-box/pull/253))
- Sharing to mail link open in new tab by default ([#250](https://github.com/k-box/k-box/pull/250))
- Update xpdf-tools to 4.01.01 ([#248](https://github.com/k-box/k-box/pull/248))
- Changing password requires to have a verified email address

### Fixed

- Allow to reset the language of a document to "no language recognized" in case the language was wrongly recognized ([#247](https://github.com/k-box/k-box/pull/247), [#215](https://github.com/k-box/k-box/issues/215))
- Ensure pagination is visible in the recents page ([#219](https://github.com/k-box/k-box/issues/219), [#246](https://github.com/k-box/k-box/pull/246))
- Listing of users that can be added as member of a project ([#211](https://github.com/k-box/k-box/issues/211), [#243](https://github.com/k-box/k-box/pull/243))
- Sharing dialog not loading if document/collection was shared to a disabled user ([#254](https://github.com/k-box/k-box/pull/254))


### Removed

- Ability to create guest users

## [0.26.0] - 2019-03-29

### Added

- Support Markdown format in document abstract ([#232](https://github.com/k-box/k-box/pull/232))
- Enable to trash files when seeing search results on the projects page ([#214](https://github.com/k-box/k-box/pull/214))

### Changed

- Changed the text used when a license is not selected
- Make K-Link guest search disabled by default ([#231](https://github.com/k-box/k-box/pull/231))
- Unify administration capabilities ([#220](https://github.com/k-box/k-box/pull/220))
  - Rename `Capability::MANAGE_DMS` to `Capability::MANAGE_KBOX`
- Rename the permission `change_document_visibility` to `publish_to_klink` ([#226](https://github.com/k-box/k-box/pull/226))

### Fixed

- Prevent share with link option to appear for collections ([#234](https://github.com/k-box/k-box/pull/234))
- Fix use explicit password during account creation ([#233](https://github.com/k-box/k-box/pull/233))
- Auto remove permission highlight after 2 seconds ([#228](https://github.com/k-box/k-box/pull/228))
- Fix share button hidden while document is indexing or without K-Link connection ([#213](https://github.com/k-box/k-box/pull/213))

### Removed

- Remove unused navigation memories tables ([#229](https://github.com/k-box/k-box/pull/229))
- Remove guest user role ([#227](https://github.com/k-box/k-box/pull/227))
- Remove K-Linker role ([#226](https://github.com/k-box/k-box/pull/226))
- Remove deprecated people group feature ([#223](https://github.com/k-box/k-box/pull/223))
- Remove import capability ([#221](https://github.com/k-box/k-box/pull/221))
- Remove unused collections listing page


## [0.25.3] - 2019-02-19

### Changed

- Update Geo plugin to version [0.2.3](./plugins/geo/changelog.md#023---2010-02-19)

## [0.25.2] - 2019-02-18

### Changed

- Update Geo plugin to version [0.2.2](./plugins/geo/changelog.md#022---2010-02-18)
- Improved English, French, German, Russian and Kyrgyz localization

### Fixed

- Automatic php max upload and post size calculation ([#200](https://github.com/k-box/k-box/pull/200))


## [0.25.1] - 2019-01-03

### Fixed

- Deleting shared collection result in unexisting page being displayed ([#194](https://github.com/k-box/k-box/pull/194))
- Upload error message not properly displayed when file size is too large ([#195](https://github.com/k-box/k-box/pull/195))
- Creating a collection with the same name as a trashed collection ([#198](https://github.com/k-box/k-box/pull/198))

### Changed

- Improved English, Russian and Kyrgyz localization ([#197](https://github.com/k-box/k-box/pull/197))

## [0.25.0] - 2018-12-04

### Added

- Privacy: support for privacy policy and consent management following EU 2016/679 (GDPR) ([#176](https://github.com/k-box/k-box/pull/176))
- Capability for project creation ([#192](https://github.com/k-box/k-box/pull/192))

### Changed

- Remove placeholder text from login screen ([#183](https://github.com/k-box/k-box/pull/183))
- Generate url when indexing files ([#187](https://github.com/k-box/k-box/pull/187))
- Rename "Personal" document section to "My Uploads" ([#193](https://github.com/k-box/k-box/pull/193))
- Admin users now see "My Uploads" under documents, instead of "Private" ([#193](https://github.com/k-box/k-box/pull/193))
- Moved all files access under storage administration ([#193](https://github.com/k-box/k-box/pull/193))

### Fixed

- Fix document icon appearing over the sidebar on mobile ([#184](https://github.com/k-box/k-box/pull/184))
- Fix regression when moving collections ([#185](https://github.com/k-box/k-box/pull/185))
- Fix error searching in shared with me section ([#186](https://github.com/k-box/k-box/pull/186))
- Fix project filters populated without projects ([#189](https://github.com/k-box/k-box/pull/189))
- Fix navigation drawer button appearing on login page ([#191](https://github.com/k-box/k-box/pull/191))

## [0.24.1] - 2018-11-20

### Changed

- Update Geo plugin to version [0.2.1](./plugins/geo/changelog.md#021---2018-11-20)

### Fixed

- Russian translation of "Add user" button in sharing dialog
- Russian translation of starred documents counter in user profile

## [0.24.0] - 2018-11-15

### Added

- Geographic data section for searching geographic files with the use of spatial filter ([#140](https://github.com/k-box/k-box/pull/140)), ([#157](https://github.com/k-box/k-box/pull/157))
- Add oEmbed support for documents ([#145](https://github.com/k-box/k-box/pull/145))

### Changed

- Uniform properties listing across detail panels ([#159](https://github.com/k-box/k-box/pull/159)) 
- GDPR clean personal data from detail panels ([#165](https://github.com/k-box/k-box/pull/165)) 
- Anonymize IP address in access log ([#167](https://github.com/k-box/k-box/pull/167)) 
- Geographic Extension plugin to version [0.2.0](./plugins/geo/changelog.md#020---2018-11-13)
- Update Video Processing CLI to version 0.5.3 ([#174](https://github.com/k-box/k-box/pull/174))

### Fixed

- Reduce impact of file indexing failures on elaboration pipeline 
- Avoid populating Authors and Uploader with user personal data when indexing ([#166](https://github.com/k-box/k-box/pull/166)) 

## [0.23.2] - 2018-10-22

### Fixed

- Excessive system resource usage while handling resumable uploads

### Changed

- The video processing is prevented on files whose size is above 200 MB

## [0.23.1] - 2018-10-09

### Fixed

-  Fix file not downloadable after publication to K-Link ([#143](https://github.com/k-box/k-box/pull/143))

### Changed

- Update streaming service client and Tus libraries ([#142](https://github.com/k-box/k-box/pull/142))
- Change default map maximum zoom to 20 ([#141](https://github.com/k-box/k-box/pull/141))

## [0.23.0] - 2018-10-01

### Added

- Kyrgyz localization ([#120](https://github.com/k-box/k-box/pull/120))
- Extensibility to the Document Elaboration pipeline ([#116](https://github.com/k-box/k-box/pull/116))
- Basic Plugin architecture. Can be enabled by using the `plugins` flag ([#118](https://github.com/k-box/k-box/pull/118))
- Add `File` and `DocumentDescriptor` delete and restore events ([#128](https://github.com/k-box/k-box/pull/128))  
- Ability to register new file types ([#130](https://github.com/k-box/k-box/pull/130))
- Thumbnails drivers with extension support ([#130](https://github.com/k-box/k-box/pull/130))
- Preview drivers with extension support ([#131](https://github.com/k-box/k-box/pull/131))
- Preliminary extension for visualizing the File properties, extracted from the file metadata, on the UI ([#133](https://github.com/k-box/k-box/pull/133))
- Static definition of document types ([#130](https://github.com/k-box/k-box/pull/130))
- [Geographic Data Plugin](./plugins/geo/changelog.md)
- Reusable map preview component ([#133](https://github.com/k-box/k-box/pull/133))

### Changed

- K-Search 3.5.0 and K-Search Engine 1.0.1 are now required. **Requires reindex**
- Update Laravel framework to version 5.5
- Improve Document Elaboration pipeline usage ([#116](https://github.com/k-box/k-box/pull/116))
- File type detection now uses a combination of mime type extraction and custom detection drivers ([#130](https://github.com/k-box/k-box/pull/130))
- File properties are now handled via the FileProperties hierarchy ([#133](https://github.com/k-box/k-box/pull/133))

### Fixed

- Video format and resolution on upload page ([#119](https://github.com/k-box/k-box/pull/119))
- The user field on project create and edit pages has now the same interface ([#127](https://github.com/k-box/k-box/pull/127))
- Fix trashing of files not added to the search engine ([#129](https://github.com/k-box/k-box/pull/129))

### Removed

- The code namespaces `\Klink\DmsDocuments` and `\Content` were removed in favor of the `\KBox\Documents` namespaces ([#123](https://github.com/k-box/k-box/pull/123))

### Deprecated


## [0.22.0] - 2018-07-26

### Added

 - German Translation ([#99](https://github.com/k-box/k-box/pull/99))
 - Linting of shell scripts via [ShellCheck](https://www.shellcheck.net/) ([#83](https://github.com/k-box/k-box/pull/83))
 - Language whitelist for document language recognition ([#42](https://github.com/k-box/k-box/issues/42) [#113](https://github.com/k-box/k-box/pull/113))
 - "Do not reply" phrase to emails ([#105](https://github.com/k-box/k-box/pull/105))

### Changed

- Configuration environment variable naming schema ([#83](https://github.com/k-box/k-box/pull/83))
- `KBOX_APP_LOCAL_URL` default value, if used from Docker, is now http://kbox/. The new default value is aligned to the example docker compose file
- `KBOX_PHP_POST_MAX_SIZE` and `KBOX_PHP_UPLOAD_MAX_FILESIZE` are automatically set based on the `KBOX_UPLOAD_LIMIT` value
- Update K-Search Client to version 3.1.0
- Use version 3.4 of the K-Search API, requires K-Search version 3.3.0 **breaking change**
- Update Tus and K-Link Streaming Client packages to require GitHub published releases
- Allow duplicated documents ([#40](https://github.com/k-box/k-box/issues/40) [#108](https://github.com/k-box/k-box/pull/108))
- Full file path doc details panel ([#110](https://github.com/k-box/k-box/pull/110))

### Fixed

- Recent do not show all available recently updated documents for a user ([#94](https://github.com/k-box/k-box/issues/94))
- Filter showing label for pot files instead of ppt
([#87](https://github.com/k-box/k-box/issues/87) [#93](https://github.com/k-box/k-box/pull/93))
- Move project to personal collection leading to unreachable collections ([#102](https://github.com/k-box/k-box/pull/102))
- Sub-collection trashing and deleting ([#101](https://github.com/k-box/k-box/pull/101))
- Improve Russian localization ([#104](https://github.com/k-box/k-box/pull/104) [#111](https://github.com/k-box/k-box/pull/111) [#112](https://github.com/k-box/k-box/pull/112))
- Language of a document wrongly displayed in the document details ([#42](https://github.com/k-box/k-box/issues/42) [#113](https://github.com/k-box/k-box/pull/113))

### Removed

- Remove deprecated import from url feature ([#100](https://github.com/k-box/k-box/pull/100))

### Deprecated

- Configuration variables whose name starts with `KLINK_PHP_` ([#83](https://github.com/k-box/k-box/pull/83)), plus
 - `KLINK_DMS_DIR`
 - `DMS_USE_HTTPS`, should not be set manually
 - `DMS_INSTITUTION_IDENTIFIER` is now deprecated, and as part of the Institution management removal will be removed in a future version
 - `DMS_IDENTIFIER` is now deprecated. The variable will be ignored if set
 - `KLINK_SETUP_WWWUSER` is now deprecated, the default user will be `www-data`
 - `DMS_ENABLE_ACTIVITY_TRACKING` was not used, so it has been deprecated. The variable will be ignored if set
 - `DMS_UPLOAD_FOLDER` was not used, so it has been deprecated
 - `DMS_RECENT_TIMELIMIT` is now deprecated as it was not used

## [0.21.2] 2018-06-18


### Fixed

- Checking of Visibility search parameter within _public_, _private_ enumeration ([#85](https://github.com/k-box/k-box/issues/85))
- Listing starred documents in case a document is in the trash ([#86](https://github.com/k-box/k-box/issues/86))
- Drag and drop of documents and collection on the sidebar in Microsoft Edge ([#81](https://github.com/k-box/k-box/issues/81), [#95](https://github.com/k-box/k-box/pull/95))
- Automated statistics extraction for the first 9 days of the month ([#92](https://github.com/k-box/k-box/pull/92))

### Removed

- Filter button from Trash, as search and filtering is not supported ([#84](https://github.com/k-box/k-box/issues/84))


### Deprecated

- `DMS_USE_HTTPS` environment variable. Will be guessed from the configured application URL

## [0.21.1] 2018-05-10

### Fixed

- Add default application internal URL in the `docker-compose.example.yml` file
- Fix application key not found when doing `docker-compose stop && docker-compose up -d` on an already configured instance

## [0.21.0] 2018-05-10

### Added

- Ability to create users even if the email server is not configured ([#77](https://github.com/k-box/k-box/pull/77))

### Changed

- Move to PHP 7.1 for the official Docker image ([#71](https://github.com/k-box/k-box/pull/71))
- Besides the project manager, no need to add additional users upon project creation  ([#70](https://github.com/k-box/k-box/pull/70)).
- The Application Key is automatically generated, if not specified in the deployment environment variables ([#72](https://github.com/k-box/k-box/pull/72)).
- **breaking change** The Application Key is now enforced to be 32 characters long ([#72](https://github.com/k-box/k-box/pull/72)).
- The command `dms:create-admin` has been renamed to `create-admin` (possible breaking change) ([#75](https://github.com/k-box/k-box/pull/75)).
- The `create-admin` does not accept `password` and `username` as arguments anymore, but uses options instead (possible breaking change) ([#75](https://github.com/k-box/k-box/pull/75)).

### Fixed

- Properly handle unescaped mime types when used as search parameters on the URL ([#65](https://github.com/k-box/k-box/issues/65)).
- Tajik translation of the copyright owner and license feature

## [0.20.1] 2018-03-15

### Fixed

- Search and Filters were not available in personal section. Now they should be back.
- Sidebar scrollbar is not too thin on Chrome anymore

## [0.20.0] 2018-03-05

### Added

- French translation
- Preview and download old versions of documents
- License and Copyright management

### Changed

- Removed the login link from the navigation menu [#12](https://github.com/k-box/k-box/pull/12), [#10](https://github.com/k-box/k-box/issues/10)
- Improved user experience of versioning
- The forgot password page now requires a minimum password length of 8 characters
- "Uploaded by" includes now the organization the uploader is member of, if available.

### Fixed

- Layout of the preview page at small resolution when a video is displayed
- Date of document in Russian UI
- Identification of the browser accepted languages
- Selection of collection filter is respected. It now has the precedence over the currently browsed collection (regression)
- Sidebar context menu in the Trash

### Removed 

- The field "Institution" in details panel, replaced with "Uploaded by"

## [0.19.1] 2017-12-21

### Fixed

- Wrong images linked in some FAQ on the Russian version

## [0.19.0] 2017-12-20

### Added

- Video playback using the Dynamic Adaptive Streaming over HTTP protocol.

### Fixed

- Restored share item in the context menu when searching from the projects page
- Re-enabling the use of some html tags in microsite content. Allowed tags are a, p, div, span, em, strong, img, br, b, style, ol, li, ul
- Direct email sending to user from the administration panel
- Project filters selection on search performed from the projects page

### Changed

- the uploader of the document will be set to the User's organization, if configured in the user profile

## [0.19.0-RC5] 2017-12-01

### Fixed

- Streaming information was not always sent when updating previously published documents

## [0.19.0-RC4] 2017-12-01

### Changed

- Image on the login image

### Fixed

- Recent page not loading if the user don't have a personal document, but only shared ones

## [0.19-RC3] 2017-11-30

### Added

- Organization name is presented on the login page

### Changed

- Document descriptors with a cancelled upload status or an in progress upload status are not listed anymore in the document sections

### Fixed

- Showing microsite creation/edit only if the project administrator is connected
- Fixed display of microsite view button if no microsite is configured for the project
- Microsite section only visible if microsite is available or the logged in user is the project manager
- Show project edit link only if the authenticated user is project manager
- Showing a proper error message if the user is trying to access a non existent file
- Cancelling an upload is now reflected on the database

## [0.19-RC2] 2017-11-23

### Added

- Migration of video file location on disk

### Fixed

- Handling of unknown mime types when processing aggregations for the elastic list
- Generation of UUID

## [0.19-RC1] 2017-11-19

### Added 

- UUID field to File model and database table
- `File::createFromUploadedFile()` for generating and persisting a File instance based on a file upload
- `File::$absolute_path` to retrieve the absolute path of the file
- Video Processing Package to enable the usage of the [Video Processing CLI](https://github.com/OneOffTech/video-processing-cli)
- `ElaborateVideo` action to the upload elaboration pipeline
- Thumbnail generation for mp4 video files
- Deprecation notice on the Institution administration feature
- Generation of the default deployment institution
- `organization_name` and `organization_website` fields to the User class
- Migration of the institution name and url to `organization_name` and `organization_website` for users that have an active affiliation
- Raw file endpoint to download file content based on file UUID. The endpoint is protected by a short lived token. This option has been added to support the full text indexing using K-Search API version 3, which requires to download the binary file as opposed to let applications send it.
- `app.internal_url` configuration and `APP_INTERNAL_URL` environment variable for changing how the K-Search download URL for private files is built
- Support for managing the asynchronous publication workflow
- Text Extraction service for extracting plain text from files. Supports docx, pptx, pdf, txt, md, kml and csv files.
- Language Guesser component from plain text file
- Publishing to the video streaming service a video, if configured, when performing the publishing to a network

### Changed

- Files are now stored in a folder based on `YEAR/MONTH/FILE_UUID`. Already added files are not affected by the change
- Stored filename is not reflecting anymore the original filename. The original filename is only available in the database using `File::$name`. This change should prevent filenames collision and duplication. In addition it should made possible to use the system on filesystem that don't support UTF-8 filenames. The change don't affect files already in the system
- `File::$path` now returns the relative path based on the `local` filesystem configured for storage
- `File::forceDelete()` now deletes also the files saved on disk
- `File::physicalDelete()` is now a `protected` method
- Updated Russian localization
- Edit page now shows option for opening sharing dialog to manage publishing operation

### Fixed

- Show the share item in the context menu only if the user can share, as well as on the details panel
- Document sidebar not appearing on Internet Explorer 9-11
- Breadcrumbs for shared collection now shows only the collection that the user has access to
- Loading shared page if a disabled user shared a collection with you

### Removed

- `DocumentsService::constructLocalPathForImport` has been removed
- Ability to edit, create and delete Institutions from the User Interface
- Microsite strong relation with the Institution of the project manager
- Ability to select user affiliation when creating a new user
- Automatic assignement of the users institution to generated DocumentDescriptors after a file is uploaded

## [0.18.0] 2017-09-25

### Added 

- redirect from `/dms/something` to `/something` as the `/dms` alias is deprecated. 
  This is only for supporting old GET urls that have been added to reports
- Tusd package for resumable file upload and tus server inside the Docker image
- Page at `/uploads` for testing uploads via tus protocol
- [docker] `/tus-uploads` in nginx alias configuration to proxy the calls to the tus server
- Added `failed_at` attribute to DocumentDescriptor
- Added `request_id`, `upload_started_at`, `upload_cancelled_at`, `upload_completed_at` to 
  linking the tus upload status to the File.
- Document elaboration pipeline for processing uploaded files
- Privacy policy

### Changed

- [docker image] moved to NGINX as integrated webserver
- [docker image] changed base image to the official PHP 7 fpm
- [docker image] by default the code is served from the root, 
  no need to specify `/dms` anymore
- upload with tus resumable protocol is not subject to File Already Exists check
- changed DocumentDescriptors statuses:
 - `STATUS_PENDING` is now `STATUS_PROCESSING` to better state that the file is being processed by some asynchronous actions
 - `STATUS_INDEXED` is now `STATUS_COMPLETED` to reflect that all operations on the file have been concluded

## [0.18.0-RC1] 2017-08-06

### Added 

- header layout component
- list item layout component
- SVG Material icons with the https://github.com/avvertix/materialicons-laravel-bridge package
- Sharing icons, accross all visualization types, on documents that are shared
- UUID field to Document Descriptor as it will be used in the next 
  version of the K-Search API.
- `UserCreatedNotification` for handling the sending of the welcome message to the newly created user
- User name of the user that uploaded a file version
- An empty document upload is now blocked with the message (key: `documents.upload.empty_file_error`)

### Changed

- Upgraded to Laravel 5.4 (see the Laravel website for the changes)
- Applied the K-Link style guide
- Sharing email notification now uses the Laravel notification provider with the default template
- Password reset notification now uses the Laravel notification provider with the default template
- Started to move the CSS towards a BEM approach and the usage of Flebox
- Moved from SweetAlert to SweetAlert2 as the original library is not maintained anymore
 - the `DMS.MessageBox` javascript calls now follows a Promise based approach.
- Details and card layouts. Now the details view has nicer columns
- Starred icons behavior change: now the icons are only two, one for starred and one for unstarred. The starred variant is colored in yellow
- The UI now uses the available system fonts
- Base font size has been increased to 16px
- New hero image on the login page
- Login page and welcome page are now the same, no more difference in layout and content
- the selection checkboxes are now SVG elements that have the same look and feel no matter of the browser default styles
- Document layouts (grid, details, tiles) to use flexbox and a new list-item component
- K-Box administrators are now redirected to the document section after logging-in
- Collection removed message is not showed anymore when a collection is removed from the document details panel
- Composer private repository is gone, now referencing directly the 
  packages from the git repository


### Fixed

- Wrong redirect after password reset complete, now points to the root page
- Wrong filters in personal section, now only the local document id is used to filter the searchable documents
- Handling of documents without language in the documents listing
- Avatar, with username initials, for Cyrillic written usernames

### Removed

- `management-layout.blade.php` as the differences with `global.blade.php` were manageable in the global layout
- Removed difference between `app_dev.less` and `app.less`, now the main style is generated from `app.less`
- Usage of Material Design PNG icons
- Bower package manager, now all frontend dependencies are managed through NPM
- Ability to navigate to the Projects and Groups section from the UI. The code for both pages is still available.
- Skeleton CSS framework dependency

## [0.17.1] - 2017-06-13

### Fixed

- A case that make the user continuously redirecting to the page that was asked before login
- Title and footer might overlap in Power Point presentation preview
- Error during Power Point preview generation when elements uses a placeholder that is not absolutely positioned

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
- Map Visualization and the `VisualizationApiController`

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
