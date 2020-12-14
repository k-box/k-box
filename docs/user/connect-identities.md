---
Title: Connect and manage third party identities
Description: 
Since: 0.32
---

The K-Box can allow the connection of third party services to your profile. These services
are used for log-in purposes.

## Connect an identity

Under your profile, navigate to the `Connected identities` section. Click on the provider of your choice, 
i.e. Gitlab or Dropbox (the provider list depends on the K-Box configuration as selected by the administrator).

You will be redirected to the provider log in page, e.g. Gitlab, and you will be asked to authorize the `K-Box application` to read your user information. The only required information are your _name_ and _email_. This information
will be used to create a link to your existing account and will not be saved on the K-Box. Once linked, you can
log in with that provider without inserting the email and password manually.


## Unlink an identity

You can unlink the third party identity or revoke the K-Box authorization at any time.

Under listed identity click the `Unlink` button and follow the on-screen messages.

Once unlinked, you cannot use it anymore for login, but your K-Box account will remain active. 
The user's profile lifespan on the K-Box is independent of the profile on the Authentication provider,
i.e. you're still able to [recover the access](./login.md#password-recovery) to the K-Box.
