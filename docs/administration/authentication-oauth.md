---
Title: OAuth Authentication providers
Description: user accounts management
Since: next
---

K-Box supports several different ways to authenticate, beyond the basic username/password authentication.
[OAuth](https://oauth.net/) providers are one of them. Most authentication providers require a `clientID` and a `secret`.
The functionality is provided by [Connect Identity for Laravel](https://github.com/OneOffTech/laravel-connect-identity/) 
and [Laravel Socialite](https://laravel.com/docs/socialite).

Authentication providers enable quick registration of users. The K-Box retains a local user profile, created using
the user data (email, name), provided by the authentication provider. The user's profile lifespan on the K-Box is independent of the profile on the Authentication provider, i.e. the user will be able to recover the access to the K-Box even if the account on the provider is blocked or deleted.

These settings are covered by [_static configuration_](../developer/configuration.md) and require the K-Box
restart to take effect.

Below are brief descriptions of how to set up each provider.

## Activating OAuth login/registration

By default log in and registration with OAuth providers is disabled and must be explicitly configured.

To activate the feature you have to specifying the providers you wish to use. This must be done
in the [static configuration](../developer/configuration.md) by setting the `KBOX_IDENTITIES_PROVIDERS`
environment variable.

```env
KBOX_IDENTITIES_PROVIDERS=gitlab
```

The variable accepts a comma-separated list of providers to enable.

```env
KBOX_IDENTITIES_PROVIDERS=gitlab,dropbox
```

Leaving it empty or set to null disables the feature (default value).


## Providers

The K-Box currently supports the following providers: [Gitlab](#gitlab) and [Dropbox](#dropbox).

### Gitlab

To allow Gitlab users to register or log in, an OAuth application needs to be configured on the Gitlab instance.
A new application can be created under `Applications` in the 
[user profile](https://gitlab.com/profile/applications) or in the 
administration area. If you have a self-hosted Gitlab instance, we suggest to configure
the application under Gitlab's administration area.

> _note_ applications do not need to be reviewed by Gitlab to be used

If you are creating the application from the administration panel, click `New Application`, while 
creating the application from your Gitlab user profile already shows the new application fields.

> _wait_ remember that the K-Box must be reachable using HTTPS, unless you are using `localhost` for tests.

You can now enter the application name, e.g. `My K-Box Gitlab connector`, and configure the various parameters.

The `Redirect URI` is important because it tells Gitlab where the user should be redirected to after it verified the credendials
and authorized the application. Considering your K-Box instance hosted on `mykbox.domain` the redirect URIs will be:

```
https://mykbox.domain/login-via/gitlab/callback
https://mykbox.domain/register-via/gitlab/callback
https://mykbox.domain/connect-via/gitlab/callback
```

K-Box requires three redirect URIs because log in, registration and connection are distinct flows.

Mark the application as `trusted` and `confidential` and select the following scopes:

- `openid`
- `read_user`
- `profile`
- `email`

You can now press `Submit`. For further information refer to 
[Configuring Gitlab as OAuth provider](https://docs.gitlab.com/ee/integration/oauth_provider.html).

Once the application is created you will be presented with the `Application ID` and `Secret`. Copy the Application ID and secret into the environment configuration (either your .env file or if you are using docker the environment variables):

```env
GITLAB_CLIENT_ID="app key"
GITLAB_CLIENT_SECRET="App Secret"
```

If you are using a self-hosted Gitlab instance you must specify its URL like:

```env
GITLAB_INSTANCE_URI="https://mygitlab.domain/"
```

### Dropbox

To allow Dropbox users to register or log in, an OAuth application needs to be configured 
on the [Dropbox Developers portal](https://www.dropbox.com/developers/apps/).

> _note_ applications might be subject to Dropbox approval

[Dropbox OAuth Guide](https://www.dropbox.com/lp/developers/reference/oauth-guide)

Press `Create app` to start the wizard. Currently the only type of application
that can be registered is the `scoped access`. Proceed by selecting `scoped access`.

Pick `App folder` as type of access since for authentication purposes the K-Box does
not need to read your whole K-Box. This option gives the K-Box access to a single
folder named like the application keeping your files safe.

Then give the app a name and press `Create app`.

By default the application is only usable by you and up-to 50 Dropbox users. You can link up 
to 500 users, by clicking `Enable additional users`, while in development status. After the 
50th user connected the application, you are required to submit the app for 
[production approval](https://www.dropbox.com/developers/reference/developer-guide#production-approval).


In the section called `OAuth2`, assuming that The K-Box is hosted on `mykbox.domain`, 
add the following redirect URIs. These redirects are used by Dropbox to bring back the 
user on the K-Box after successfull authentication.

```
https://mykbox.domain/login-via/dropbox/callback
https://mykbox.domain/register-via/dropbox/callback
https://mykbox.domain/connect-via/dropbox/callback
```

> _wait_ if you are configuring a production instance, remember that the K-Box must 
be reachable using HTTPS.

The K-Box will receive a token that grants access to Dropbox on behalf of the user. 

Keep `Short-lived` as `Access token expiration`. Using short lived tokens improves the security 
of user's Dropbox account.

Under the `Permissions` section the suggested configuration, ticking only `account_info.read` 
is enough for the authentication and registration flows.

Now copy the `App key` and `App secret` into the environment configuration (either your .env file or if you are using docker the environment variables)

```env
DROPBOX_CLIENT_ID="app key"
DROPBOX_CLIENT_SECRET="App Secret"
```

## Log in and registration

The K-Box uses the OAuth providers to defer the authentication of the user and to obtain the minimum 
information to verify who user claims to be. Authorization of the user to perform actions within the K-Box is controlled
by the account configuration on the K-Box itself.

Authentication is the action of verifying that the user is who s/he claims to be, while authorization is the
process of validating the user permission to access a resource or function.

The external providers are therefore used for user registration and log in actions.

The two flows follow the same high level approach:

1. User selects action (log in or register)
2. User is redirected to provider to prove his/her identity (authentication)
3. User gets redirected to K-Box
  - For registration, the K-Box pulls email, name and identifier as given by the provider and creates an account
  - For log in, the K-Box compares the provider's identifier to the stored copy and, if an account is found, grants access

It must be noted that the K-Box enforces the uniqueness of the email address, so
if user attempts to register using two authentication providers that expose the same
email address the second registration will fail. On the other end, if user has, for example,
both Gitlab and Dropbox accounts registered with different email addresses, the registration on the 
K-Box done with both will succeed and the user will be given two separate K-Box accounts.

## Multiple connected providers

Each user might have multiple identities provider connected. The K-Box allows all of them
to be used for the log in action.

## Recovering access to the K-Box in case the provider's account is blocked or deleted

The K-Box stores a local account for users. Therefore, if user loses access to the external 
provider, s/he can still access the K-Box via username/password.

To obtain the password, user must ask for the password recovery feature and input
the same email address as the one in the external provider _used at the time of registration_.
