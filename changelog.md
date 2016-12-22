# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/0.3.0/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

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
