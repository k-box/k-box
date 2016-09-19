<!-- 1 -->
# Introduction

The K-Link Document Managament System (DMS) is a web application designed for document management inside an Organization or in the context of a Project involving more Organizations.

The DMS is built on top of [Laravel 5.1](http://laravel.com/docs/5.1/) and PHP 5.5. It is available in two edition: (1) Standard and (2) Project Edition.

**Standard Edition**

The Standard edition has been developed with a single Organization in mind, that has to manage its own private documents and wants to control the access on those documents, but on the other hand need the ability to make some documents publicly available without a complex flow.

The build of the Standard edition is available as a [compressed file](http://temp.klink.dyndns.ws//builds/klink-dms-master.tar.gz) and as klinkdocker_dms Docker image on the K-Link Docker registry.


**Project Edition**

The Project edition is tailored for managing documents that belongs to Projects that involve many Organizations. Projects are managed by a specific type of user and the access to a project is controlled by a user management.

The build of the Project edition is available as a [compressed file](http://temp.klink.dyndns.ws//builds/klink-dms-project-edition.tar.gz) and as klinkdocker_dmsproject Docker image on the K-Link Docker registry.


Developers will find the standard edition in the `master` branch of the [klinkdms/dms](https://gitlab.klink.dyndns.ws:3000/klinkdms/dms) repository, while the project edition is in the `project-edition` branch in the same repository.


## Standard edition

Main features?

Main flows?


## Project Edition



What is the DMS Project Edition?

How differs from the standard DMS?

- Has the Project concept, collections created under a project are defined as Project Collections
- Don't have the Institutional Collections
- Every project has a root Project Collection named with the same name as the Project
- A project can have only one root Project Collection
- Projects can be created by Project Administrators
- Each Project Administrator can manage only Projects that he/she has created
- Users must be granted in order to access the project and its collections


How is the project concept implemented?

- refer to the [database](./database) section

Current Limitation of the strategy?

- A project can only have one Project Administrator
- Other Project Administrators cannot manage project collections or project documents unless explicitly added to the users of the project
- K-Box Administrator cannot manage project collections unless explicitly added to the users of the project
- A user can belongs to a single Institution and is not configurable for each project


Project flow? what happens when a project is created


## Contribution

**If you want to contribute to the DMS please have a look at the [developer installation guide](./developer-installation)**
