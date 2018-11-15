---
Slug: 0.24.0
PageTitle: K-Box v0.24 (October 2018)
Order: 0
---

Welcome to the October 2018 release of the K-Box. This release continue the work on the plugin architecture and the Geographic Extension introduced in the previous release. In addition with this release we are starting the process to make the K-Box compliant with the General Data Protection Regulation (GDPR - EU 679/2016)

- [Geographic Extension](#geographic-extension)
- [GDPR](#gdpr)
- [Other changes](#other-notable-changes)
- [Upgrade](#upgrade)

### Geographic Extension

In this version we improved the Geographic Extension in many areas. The most prominent one is the ability to search for files within a given geographic region. We call this _Spatial filtering_.

There are general improvements over file format handling and one of them is the GeoPackage v1.2 format support.

Last, but not least, we added the ability to see properties of the entities included in geographic files. For example if your file define the buildings of a city, and for each of them contains various attributes, you are now able to see those attributes by clicking on the building inside the map preview.

### GDPR

With this release we are changing how the K-Box deals with user personal data.

To respect the General Data Protection Regulation (EU 679/2016):

- the user name and email are not included anymore in the K-Link publication, instead an anonymized identifier is used
- the document details panel shows only the uploader user name instead of the username and the email address
- when sharing with public link the uploader user information are not presented unless the receiver is logged-in
- IP addresses in logs are anonymized

These are only the preliminar changes that we are introducing to respect the privacy of our users.

### Other notable changes

If you are a developer or you maintain a K-Box installation, please have a look 
at the [changelog](../../changelog.md) for a complete list of changes.

### Upgrade

This K-Box version **requires K-Search 3.6.0** and rely on changes introduced in that version at the K-Link level. For this reason a full reindex of public document must be executed.
