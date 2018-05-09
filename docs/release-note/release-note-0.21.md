---
Slug: 0.21.0
PageTitle: K-Box v0.21 (May 2018)
Order: 0
---

Welcome to the May 2018 release of the K-Box. This release, among all other changes, includes two new features.

- [Project creation without adding members](#project-creation-without-adding-members)
- [Create user when email server is offline](#create-user-when-email-server-is-offline)
- [Other changes](#other-notable-changes)
- [Upgrade](#upgrade)

### Project creation without adding members

You can now create a project without adding other users. The project will show zero members and will only be visible to you.
You can still add members later.

### Create user when email server is offline

Sometime is not possible to configure the E-Mail server or it just won't work in your country. In this version we added the possibility to specify a password when a new account is created.

The automatic generation of the password is still available, but only if the E-Mail configuration is valid.

### Other notable changes

- Improved document type selection for SVG files
- Improved installation documentation
- Improved developer installation documentation

### Upgrade

This version include a **breaking change** in the application key management. 
The application key must be 32 characters. This will invalidate all user sessions and will require all users to log-in again.

