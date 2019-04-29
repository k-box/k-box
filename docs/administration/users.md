---
Title: Users management
Description: user accounts management
---

# Users management

The K-Box user management is based on [permissions](./permissions.md). 
To facilitate the user creation the permissions are grouped into three roles:

1. Partner: Can upload, edit and share files. Has access to shared Projects and Personal space
2. Project Manager: Has all _Partner_ permission, plus can manage projects and publish (or unpublish) data to the K-Link
3. K-Box Administrator: Has all permissions and can manage the K-Box configuration

> For all the available permissions, please refer to the [Permissions List](./permissions.md)


## User registration

The K-Box can be configured to accept user registration.
The user self registration will be available as a separate page under `/register`.
Links from the login page will be presented automatically.

The created accounts will have _Partner_ role.
After the account creation the user will be asked to confirm the email address.
As of now email verification can be skipped, but in a future version will be
enforced for security reasons.

To enable the user registration include the following line into the environment configuration

```conf
KBOX_USER_REGISTRATION=true
```

Once changed, if you are using Docker restart the instance, otherwise clean the cached configuration and routes

```
php artisan config:clear

php artisan route:clear
```

This is required to make sure visitors will be able to access the register page.

## Create users

Users can be created from the Administration section.

> _Only administrators_ are entitled to create a new account for a user.

![Admin](../user/images/admin-page.PNG)

To add new user

1. From the “Administration” page select “Accounts”
2. Click on “Create user”
3. Fill in the email address, the username and select the role.
   If necessary you can set a password for the user and customize the permissions
4. Once done, click “Create” at the bottom of the page.

