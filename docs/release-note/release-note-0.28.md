---
Slug: 0.28
PageTitle: K-Box v0.28 (June 2019)
Order: 0
---

Welcome to the June 2019 release of the K-Box. With this release, we are improving the configuration and the documentation of the K-Box.

- [Analytics settings](#analytics-settings)
- [Support settings](#support-settings)
- [Documentation](#documentation)
- [Upgrade](#upgrade)

### Analytics settings

In this release we separated the analytics configuration from the settings page. 
While doing so we added the ability to use a custom Matomo instance or switch to Google Analytics.

[Find out more in the documentation](../administration/analytics.md)

### Support settings

It was the right time to give separate the support configuration from the crowded settings page.

The new section keeps the same configuration option to use UserVoice as a support service.

With this change we paved the way for new support service providers.

At [documentation](../administration/support.md) level, we made clear what data and personal data are shared with the service

### Documentation

We noticed that talking about backups an explanation on how to restore them was missing, therefore we added it.
In addition we made clear how to change a domain the K-Box is running on.


### Upgrade

This version include changes at configuration level. 
We recommend having a full backup before doing the upgrade.

- The upgrade will disable the UserVoice support, if was enabled before. 
  To keep it active you can set `KBOX_SUPPORT_SERVICE: true` in the environment configuration or enter the new support 
  administration section and press "save support settings".
- The upgrade change how [analytics configuration](../administration/analytics.md) is managed. If it was enabled, the upgrade will disable it.
  To keep using the analytics you have to enter the analytics administration section and complete the configuration.
  You can do the operation also via environment variables.


_If you are a developer or you maintain a K-Box installation, please have a look at the [changelog](../../changelog.md) for a complete list of changes._