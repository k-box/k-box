---
Slug: 0.27
PageTitle: K-Box v0.27 (May 2019)
Order: 0
---

Welcome to the May 2019 release of the K-Box. With this release we are improving the login and registration process, as well as continously improving the reliability of the K-Box.

- [Updated Logo](#updated-logo)
- [New login layout](#new-login-layout)
- [User Registration](#user-registration)
- [E-Mail verification](#disappearing-sharing-button)
- [Other changes](#other-notable-changes)
- [Upgrade](#upgrade)

### Updated Logo

We started the process of improving the perception of the K-Box as a service of K-Link and not the K-Link itself.

To this aim we decided to use the official K-Box logomark and logotype.
You will see these changes in the icon on the browser tab and in the logo presented on the top left corner of the user interface.

### New login layout

It was time to give a fresh look to the first page that new and returning visitors are seeing: the login page.

The new layout maintain the right alignment of the login form, but uses a new container structure for the image.

This new layout is also used for the password recovery page and the user registration form.

### User Registration

Administrators of a K-Box can now accept new users registration. This is an opt-in option and is 
not enabled by default.

When enabled visitors will see a "Register" link on the login page. They will be asked to insert an E-Mail address and a password before gaining access to an account. The newly created account will have a Partner role.

### E-Mail verification

To improve security we are introducing an E-Mail verification procedure. The verification consists in an email that contain a "Verify" link that should be clicked by the user.
In this way we can ensure that an existing E-Mail addresses has been used.

The E-Mail verification is currently enforced in the following cases:

- New user registration using the public user registration
- When changing password and email from the user profile page


### Other notable changes

- Auto remove user account permission highlight after 2 seconds 
- Changed the text used when a license is not selected
- Enable to trash files when seeing search results on the projects page
- Fix setting a password during account creation 

_If you are a developer or you maintain a K-Box installation, please have a look at the [changelog](../../changelog.md) for a complete list of changes._

### Upgrade

The version include changes at the database level. 
We recommend to have a full database backup before doing the upgrade.

In version 0.26 guest users have been deprecated. This version do not allow to create new
guests users. Existing guest users will continue to work.
