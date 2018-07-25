---
Slug: 0.22.0
PageTitle: K-Box v0.22 (July 2018)
Order: 0
---

Welcome to the July 2018 release of the K-Box. 

- [Allow upload of duplicated documents](#allow-upload-of-duplicated-documents)
- [German localization](#german-localization)
- [Collection management](#collection-management)
- [Other changes](#other-notable-changes)
- [Upgrade](#upgrade)

### Allow upload of duplicated documents

Previously the K-Box blocked uploads of files that were already present, even if the file was in the personal space of another user.
With this version you can now upload 

- duplicates of existing file
- a previous version of an existing document as a separate entry

When you upload a file, if the already existing one is in a location you have access (e.g. in a project, in your personals), you will receive an email notification indicating where to find the already existing one and what is the duplicate. 

On the edit page of each duplicate you will see a new section, called "Duplicates", from which you can decide to reuse the existing document, or to keep the duplication.

The duplication check is performed only at the time of a file upload. A duplicate might be recognized within 10 minutes from the upload, while the email notification will always be sent 30 minutes after the discovery of the first duplicated upload.

For more information please consult the [Duplicate Documents section in our user manual](../user/documents/duplicates.md)

### German localization

If your browser preferred language is German, you might find that the K-Box User Interface has been also translated.

This is a community contribution, if you want to improve it feel free to submit pull requests to https://github.com/k-box/k-box

### Collection management

Collection management has been improved in two areas: (1) move from project to personal and (2) trashing of collection with sub-collection.

In the past moving projects collections to personal might lead to not seeing anymore some collections. This was caused by the fact that collections might be created by other users, and therefore owned by them. If the collections you are moving are not entirely owned by you, the move action will now be denied.

In previous versions trashing collections that contains sub-collections moved to the trash only the first collection. Now all sub-collections are moved to the trash as well.

### Other notable changes

- Unknown language labels will not appear anymore in the filters or on the document details
- Improved recents page document listing
- Fixed filter showing pot instead of ppt for Power Point files
- Various Russian localization improvement

If you are a developer or you maintain a K-Box installation, please have a look 
at the [changelog](../../changelog.md) for a complete list of changes.

### Upgrade

This K-Box version **requires K-Search 3.3.0**. If you use an older version please upgrade.

This release starts a series of changes to the environment configuration required for deploying the K-Box. The old configuration is still supported, but deprecated and will be removed in a future version.

Among all the changes, summarized in the [Environment variable name changes section of the configuration guide](../developer/configuration.md#environment-variable-name-changes), the following might directly affect your setup:

- remove `DMS_USE_HTTPS` from the docker-compose file, as it will be automatically recognized, based on the application url, at startup
- use `KBOX_UPLOAD_LIMIT` instead of manually setting `KBOX_PHP_POST_MAX_SIZE` and `KBOX_PHP_UPLOAD_MAX_FILESIZE` for defining the maximum allowed file size for upload. Here you need to express a value in Kilobytes, default is 204800
- If you are using Docker and the your setup is based on the docker-compose.example.yml file, you can safely remove the `KLINK_DMS_APP_INTERNAL_URL` variable
