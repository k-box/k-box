---
Slug: 0.26
PageTitle: K-Box v0.26 (March 2019)
Order: 0
---

Welcome to the March 2019 release of the K-Box. This release marks the beginning of user management
improvements, plus the usual bugfix and two small additions.

- [Improved abstract format](#improved-abstract-format)
- [Disappearing sharing button](#disappearing-sharing-button)
- [User roles and permissions](#user-roles-and-permissions)
- [Other changes](#other-notable-changes)
- [Upgrade](#upgrade)
- [Deprecations](#deprecations)

### Improved abstract format

We received feedbacks about the abstract field not respecting new lines. So we decided to
give some minimal formatting options.

The abstract field now keep paragraphs, to do this just insert a single empty line between
the two paragraphs:

```md
This will be the first paragraph

This will be the second paragraph
```

Additionally you can use lists. Just include the `-` character at the beginning of each
line, like:

```md
The entry below will be an unordered list

- First item
- Second item
- Third item
- ...
```

Sometimes a bold statement is necessary, that's why we added the support for writing
in bold. To do that wrap the words around two asterisks `**`:

```md
A **single** word in bold or **multiple words in bold**
```

We didn't include a visual editor yet, but it is in our plans.

### Disappearing sharing button

We received emails about the sharing button disappearing from the edit page.
To figure out what was happening we had a chat with the Ghostbusters, but in the end no ghosts were involved.

Now the sharing button included in the document edit page should stay visible.

### User roles and permissions

The community raised some concerns over accounts permission that were not connected to current
features and actions ([#36](https://github.com/k-box/k-box/issues/36), [#206](https://github.com/k-box/k-box/issues/206))
so in this version we cleaned and reorganized the permissions and the default user roles.

Now there are only 3 predefined roles: Partner, Project Manager, Administrator. 
Guest and K-Linker are now represented using permissions only. In addition unnecessary permissions have been
removed to ensure working accounts being created.

### Other notable changes

- Auto remove user account permission highlight after 2 seconds 
- Changed the text used when a license is not selected
- Enable to trash files when seeing search results on the projects page
- Fix setting a password during account creation 

_If you are a developer or you maintain a K-Box installation, please have a look at the [changelog](../../changelog.md) for a complete list of changes._

### Upgrade

The version include changes at the database level, a security patch and a configuration breaking change. 
We recommend to have a full database backup before doing the upgrade.

The `KBOX_ENABLE_GUEST_NETWORK_SEARCH` configuration default value has been changed to `false`.
This means that not logged-in users will not be able to search on the connected K-Link.
If you didn't use this feature in the past, this change will not affect you. Now this
feature is not enabled by default and you must explicitly activate it.

### Deprecations

The Guest user role is now deprecated. It is still possible to have guest users and to create guest users, but it will be removed in a future version.

