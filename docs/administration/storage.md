---
Title: Storage
Description: storage handling details
---

# Storage




## User Quota

You can configure the amount of storage a user can use on the K-Box.

The main configuration happens at deploy time. You can decide a default general value to use for all old and 
newly created users, as well as the threshold that will be used to trigger the limit approaching notification.

The environment variables that let you customize the default storage quota behavior are:

- `KBOX_DEFAULT_USER_STORAGE_QUOTA` The available amount of storage to assign to a user in bytes
- `KBOX_DEFAULT_STORAGE_QUOTA_THRESHOLD_NOTIFICATION` The used threshold after which the user will be notified on the amount of free storage space


Beside the general default each user has a specific `UserQuota` entity that defines the customization and the status of the storage.
For each user the quota limit and threshold can be configured separately.

As a general rule the user specific configuration has priority over the K-Box default instance configuration.


### Checks

The K-Box performs automatic calculation of used storage quota:

- after a new file is uploaded
- every day

Is also possible to force the re-calculation of the used space (for all or specific users) by executing
the [`quote:check` command](../developer/commands/quote-check.md).

### Notifications

The user will be notified when the configured threshold is reached and when the used storage space is above or equal to the 99% of space.

Each notification will be sent only once.
