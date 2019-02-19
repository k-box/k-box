---
Slug: 0.25
PageTitle: K-Box v0.25 (November 2018)
Order: 0
---

Welcome to the November 2018 release of the K-Box. This release continue the work to make the K-Box compliant with the General Data Protection Regulation (GDPR - EU 679/2016), plus fixes some bugs and regressions

- [Privacy and consent management](#privacy-and-consent-management)
- [From Personal to My Uploads](#from-personal-to-my-uploads)
- [Other changes](#other-notable-changes)
- [Upgrade](#upgrade)

### Privacy and consent management

The K-Box is now capable of asking the user to review and agree to the Privacy Policy on first login.
In addition the user can decide if usage statistics of his/her activity can be tracked.

The privacy preferences can be managed by going into the User Profile (click on the avatar on the top right corner).

### From Personal to My Uploads

In an ongoing effort to make the K-Box navigation more clear, the "Personal" section has been renamed to "My Uploads".

This will further clarify that in that area you can see the files you (as the user) uploaded.

### Other notable changes

- Sometimes moving a collection didn't behave as expected. This should now be fixed
- We fixed to mobile related issues, the first was a menu icon appearing on the login screen and the second was a document icon floating around in the documents section
- Lastly we made sure that searching in shared with me works if you have collections too

_If you are a developer or you maintain a K-Box installation, please have a look at the [changelog](../../changelog.md) for a complete list of changes._

### Upgrade

This K-Box version **requires K-Search 3.6.0** and rely on changes introduced in that version at the K-Link level. A reindex is suggested to make sure that all published files are reachable using the new URL formats.

### Patch release 0.25.1

- Fixed deleting shared collection result in unexisting page being displayed
- Fixed upload error message not properly displayed when file size is too large
- Fixed creating a collection with the same name as a trashed collection
- Improved English, Russian and Kyrgyz localization

### Patch release 0.25.2

- Update Geo plugin to version 0.2.2
- Improved English, French, German, Russian and Kyrgyz localization
- Automatic php max upload and post size calculation

### Patch release 0.25.3

- Update Geo plugin to version 0.2.3
- Fixed Geographic data section loading error
