---
Title: Registering an account
Description: 
---

To access any K-Box, you need to register an account manually or by using one of the supported 
external services (if the desired instance has enabled these services). 
After successfully registering, you can [log-in](./login.md).

The registration might be self-made, based on invitation or done by an administrator
on your behalf.

## Manual registration

You can create an account by providing your email and password.

The K-Box requires a verified email address, this can be done by simply clicking on the link sent to the registered email.

K-Boxes can be configured to use custom parameters, therefore, each might restrict registration to invited users only or disable the manual registration. If you are unsure how to register, please contact your K-Box administrator.


## Registration by administrator

The administrator can [create accounts](../administration/users.md) on behalf of users. 

You will receive an email with the account information and the generated password.
On the first login you will be asked to confirm the email address. To do so you will
receive an email with a confirmation link. This ensures that the email address is valid.

After logging in for the first time, please go to your profile and change the password.


### Register using third party providers

If enabled, K-Box allows account creation via third party identity providers (e.g. Gitlab, Dropbox,...).

On the registration page click on the provider of your choice. You will be redirected to the provider log-in page, e.g. Dropbox. You will be asked to authorize the `K-Box application` to read your user information. The only required
information are _name_, _email_ and _avatar_. This information will be used to create a local K-Box account.

You can manage the connected identities from your [profile](./connect-identities.md).


## Choosing a nicename

After you have successfully registered, your nicename or display name will be set to the name you have in your email address.
Your username will always be the email address as K-Box requires unique usernames. However, display names can repeat. For example, there can be only one user with the username johnsmith@email.address but there can be more than one user with the display name John Smith.

To change your display name visit the profile page and change the nicename under the information section.

The display name will be used, when user information must be presented, whereas your username (email address) will
be kept private.
