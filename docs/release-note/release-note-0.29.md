---
Slug: 0.29
PageTitle: K-Box v0.29 (December 2019)
Order: 0
---

Welcome to the December 2019 release of the K-Box. This release comes after several months of work and is packed with lot of changes that will be used as the basis for more features to come. Here are the major ones:

- [User invitation](#user-invitation)
- [Shared collections navigation](#shared-collections-navigation)
- [Storage quota](#storage-quota)
- [Evolution of the user interface](#evolution-of-the-user-interface)
- [Minor additional changes](#many-other-changes-under-the-hood)

### User invitation

User registration and management was limited to administrators, but we are now opening-up the possibilities with direct invitation.

Any user can invite a person, given the email address, to register an account and join the K-Box. The person is free to set the email address and the password.


> User invitation works only if the K-Box instance allows public user registration.

[Documentation](../user/invite.md)

### Shared collections navigation

Navigating into shared collections was not always comfortable and definitely applying a shared collection to a document was not an easy task.
To resolve the problem and to give the visibility of the hierarchy of those shared collections we added a new "Shared collections" section to the sidebar.

From that section you can navigate directly to the shared collection you want without searching in the shared-with-me section. In addition you can drag and drop a document to apply collections more easily.


### Storage quota

The K-Box now supports per user storage quota, this means that you can now limit how much storage space will be used by each user. 

Only the files uploaded by the user will count on the quota usage.

[Documentation](../user/user-quota.md)

### Evolution of the user interface

This is probably the most striking change and we are aware of how it can affect the user experience, being the User Interface THE very critical aspect of an online based file sharing system. We received lot of feedbacks especially from smartphone user and we think time is ripe to get a first feedback. We are in the early stages of the process, but we believe the proposals presented in this release represent a step forward.

#### Mobile friendliness

Accessing the files can be needed on the go using a smartphone. In this release we have focused on the navigation of documents and listing page browsability from mobile device.

To ensure  all menus are correctly visualized even from mobile, the top navigation bar has been rewritten from scratch . This targets the menu previously presented  top right corner next to the avatar. It is now visible on click and presents the profile as well as the logout options. For the sake of visibility, the help menu is now placed in the top area as well.

#### Colors

We shifted to gray tones instead of the bold blue header of previous releases. This is inline with the vision to put the content and the user at the center, but
also part of a general color usage improvement. The previous version used 40 different shades of gray, with this release we reduce them of the 25% without loosing  usability.


#### Selection of user in the sharing dialog

In case of large teams the listing of users selected for sharing can be difficult to use. In this version the listing is not visible by default but created against user editing. Typing a portion of the username (3 characters minimum) the list of matching names is returned. In addition you can paste an email address to find an existing user by its email address.


### Many other changes under the hood


- Choosing "details" from the right click menu on a collection now shows a details panel, like the one for a document
- Users can update the organization in the profile without changing the nicename
- The language preference, in the profile page, now correctly reports the current language
- Uniformed the date and time presentation to report also the timezone when needed
- MP3 Audio files preview
- Honour the rotation of an image on the preview and the thumbnail


### Deprecations

Project microsites are deprecated and not enabled by default. They can still be enabled, but they might be removed in a new feature. This decision was taken because of lack of usage of the feature.

### Upgrade

- Audio preview might not work for previously uploaded mp3 files as the K-Box was not correctly recognize the file format. An automated fix is not available yet;
- Users are asked to confirm the email address to edit/change sensitive information like password or to use the invite feature.


_If you are a developer or you maintain a K-Box installation, please have a look at the [changelog](../../changelog.md) for a complete list of changes._