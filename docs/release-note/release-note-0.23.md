---
Slug: 0.23.0
PageTitle: K-Box v0.23 (September 2018)
Order: 0
---

Welcome to the September 2018 release of the K-Box. This release contains some nice additions, the usual set of bug fixes and an experiment

- [Kyrgyz localization](#kyrgyz-localization)
- [Experiment](#experiment)
- [Other changes](#other-notable-changes)
- [Upgrade](#upgrade)

### Kyrgyz localization

If your browser preferred language is Kyrgyz, you might find that the K-Box User Interface now respect your preference.

This is a community contribution, if you want to improve it feel free to submit pull requests to https://github.com/k-box/k-box

### Experiment

With this release we are overhauling the internals of the K-Box to make the space for a [Plugin architecture](../developer/plugins/plugins.md).

A plugin architecture enable adding, enabling and customizing features based on the specific needs instead of relying on K-Box release pace.

For now the Plugin architecture is experimental and disabled by default. You can enable it via deployment configuration. If you are a developer we cannot wait to receive your feedback.

To celebrate the experimental Plugin architecture, we are including a [Geographic Extension](../../plugins/geo/readme.md) that adds support for geographic files, like GeoTiff, ESRI Shapefiles, GPS tracker recordings (GPX format) and Google Earth files.

### Other notable changes

- Update Laravel framework to version 5.5
- Added video format and resolution on upload page
- The user field on project create and edit pages has now the same interface
- In the past trashing a document might be rejected due to unspoken errors even if you had the correct permission. This time your will should be respected

If you are a developer or you maintain a K-Box installation, please have a look 
at the [changelog](../../changelog.md) for a complete list of changes.

### Upgrade

This K-Box version **requires K-Search 3.5.0 and K-Search Engine 1.0.1**. 
For this reason a full reindex is required to use the search feature.
