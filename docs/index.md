---
Title: K-Box Documentation
Description: The landing page with content
---

# K-Box Documentation

The K-Box is a web application designed for handling document management inside an Organization.

> Last version: **0.23** ([Release notes](./release-note/release-note-0.23.md))

#### Browser support

K-Box supports the latest versions of modern browsers (Microsoft Edge, Chrome, Firefox, Safari and Opera) as well as
Internet Explorer 11. Internet Explorer version 9 and 10 are supported on best-effort.


## Installation

- [Requirements](./installation/requirements.md)
- [Installation](./installation/installation.md)
- [Deployment configuration](./installation/deploy-configuration.md)
- [Reverse proxy](./installation/reverse-proxy.md)


## Daily usage

- [First encounter](./user/index.md)
- [Search](./user/search.md)
- [Organize](./user/files-organization.md)
- [Upload](./user/upload.md)
- [Edit](./user/edit.md)
- [Share](./user/share.md)
- [Publish](./user/publish.md)
- [Delete](./user/delete.md)
- [License and Copyright](./user/licenses.md)
- [Learn about Errors](./user/error.md)

**Digging deeper**

- [Duplicate documents management](./user/duplicates.md)
- [File type detection](./user/document-types.md)
- [Microsites](./user/microsites.md)

## Administration

- [Users](./administration/users.md)
- [Projects](./administration/projects.md)
- [Notifications](./administration/mail.md)
- [K-Link](./administration/network.md)

## Maintenance

- [Logging](./maintenance/logging.md)
- [Backup](./maintenance/backup.md)
- [Troubles, check the known source of problems](./maintenance/troubleshooting.md)

## Developers

- [Develop for the K-Box](../developer/index.md)

## K-Box Architecture

The K-Box architecture is based on components:

1. The K-Box Application
2. The database
3. The [K-Search](https://github.com/k-box/k-search) and its [engine](https://github.com/k-box/k-search-engine/)

The K-Box (web) Application is the user facing part, while the K-Search offers the full text based retrieval of documents.

> For more information take a look on the [K-Link Technology website](https://k-link.technology/technology.html#k-box)