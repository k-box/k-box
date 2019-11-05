# K-Box Configuration

The K-Box configuration parameters are divided into two categories: static and dynamic.

Static configuration refer to settings expressed at deploy time. Those settings cannot 
be edited from the K-Box while is running.
Dynamic configuration refer to options that can be expressed at deploy time and edited 
from the user interface, or entirely configurable from the user interface.

- [Static Configuration](#static-configuration-env-file)
- [Dynamic Configuration](#dynamic-configuration)
- [Docker image configuration](#docker-image-configuration)

## Static Configuration, .env file

The static configuration is determined by the environment configuration, usually stored
in the `.evn` file. The `.env` file is located in the root of the project. 
An example environment file is in `env.example`.

The next table shows the K-Box specific configuration parameters:

| variable                              | required | type    | default value | description |
|---------------------------------------|----------| --------|---------------|-------------|
| `APP_ENV`                             | ✓        | string  |               | the environment under which the application is running, e.g. local, production |
| `APP_DEBUG`                           |          | boolean | false         | Activate the debug mode |
| [`APP_KEY`](#application-key)         | ✓        | string  |               | Encryption Key. See [Application Key section](#application-key) |
| `APP_URL`                             | ✓        | string  |               | The url on which the K-Box will be reachable, e.g. https://my.kbox.tld/ |
| `APP_INTERNAL_URL`                    | ✓        | url     |               | The URL on which the K-Box listen for file download request issued by the K-Search |
| `KBOX_DB_USERNAME` (`DB_USERNAME`)    |          | string  | dms           | The database user that can access `KBOX_DB_NAME` |
| `KBOX_DB_PASSWORD` (`DB_PASSWORD`)    | ✓        | string  |               | The database user password |
| `KBOX_DB_NAME` (`DB_NAME`)            |          | string  | dms           | The Database name |
| `KBOX_DB_HOST` (`DB_HOST`)            |          | string  | 127.0.0.1     | The database sever host |
| `KBOX_DB_TABLE_PREFIX` (`DB_TABLE_PREFIX`) |     | string  | kdms_         | The table prefix for each database table. To increse the security of the installation set you own value |
| `KBOX_SEARCH_SERVICE_URL`             | ✓        | url     |               | The URL of the K-Search that will deliver the full text search service. |
| `KBOX_ENABLE_GUEST_NETWORK_SEARCH`    |           | boolean | false          | Enable guests to search over the configured K-Link |
| `KBOX_UPLOAD_LIMIT` (`UPLOAD_LIMIT`)  |           | integer | 204800         | Maximum allowed file size for upload. Expressed in KB |
| `KBOX_MAIL_ENCRYPTION`                |           | string  | tls           | The mail encryption to use. Set to an empty string to allow insecure connections |
| `KBOX_MAIL_DRIVER`                    |           | string  | smtp          | The Email driver. See [Configuring E-Mail](../administration/settings/mail.md) |
| `KBOX_MAIL_HOST`                      |           | string  |               | The E-Mail server host |
| `KBOX_MAIL_PORT`                      |           | number  | 587           | The E-Mail server port |
| `KBOX_MAIL_FROM_ADDRESS`              |           | string  |               | The address to use when sending emails|
| `KBOX_MAIL_FROM_NAME`                 |           | string  |               | The name to show when sending emails|
| `KBOX_MAIL_USERNAME`                  |           | string  |               | The E-Mail server authentication |
| `KBOX_MAIL_PASSWORD`                  |           | string  |               | The E-Mail server authentication |
| `KBOX_SUPPORT_TOKEN` (`SUPPORT_TOKEN`)|           | string  |               | The Authentication token for the support service (can be configured from the UI). Deprecated, [use `KBOX_SUPPORT_USERVOICE_TOKEN`](../administration/support.md) |
| `KBOX_PAGE_LIMIT`                     |           | number  | 12            | The default number of items per page to show |
| `KBOX_USER_REGISTRATION`              |           | boolean  | false        | Enable or disable self user [registration](../administration/users.md#user-registration) |
| `KBOX_USER_REGISTRATION_INVITE_ONLY`  |           | boolean  | false        | Require invitation to allow user [registration](../administration/users.md#limit-to-invites-only). |
| `KBOX_ANALYTICS_SERVICE`              |           | string  | matomo        | The analytics tracking provider. Available: matomo, google-analytics |
| `KBOX_ANALYTICS_TOKEN`                |           | string  |               | The analytics token to use for the specific analytics tracking provider |
| `KBOX_SUPPORT_SERVICE`                |           | string  | null          | The support service to use. See [Configuring Support service](../administration/support.md) |
| `KBOX_DEFAULT_USER_STORAGE_QUOTA`     |           | null|int  | null        | The available amount of storage to assign to a user in bytes. See [Storage](../administration/storage.md) |
| `KBOX_DEFAULT_STORAGE_QUOTA_THRESHOLD_NOTIFICATION`     |           | int  | 80        | The used threshold after which the user will be notified on the amount of free storage space. See [Storage](../administration/storage.md) |

> `KBOX_MAIL_*` parameters can be configured from the User Interface, see [Configuring E-Mail](../administration/mail.md).

> Analytics service configuration options are available. See [Configuring Analytics service](../administration/analytics.md).

> For the resumable upload, via tus.io, configuration please refer to https://github.com/OneOffTech/laravel-tus-upload#advanced-configuration

The following block is a non exhaustive example of a `.env` file.

```
APP_ENV=local 
APP_DEBUG=false
APP_KEY=%RANDOM_STRING%
APP_URL=http://localhost/dms/
APP_INTERNAL_URL=http://docker.for.win.localhost:8000/
KBOX_DB_USERNAME=dms
KBOX_DB_PASSWORD=&middot;&middot;&middot;&middot;
KBOX_DB_HOST=localhost
KBOX_DB_NAME=dms
KBOX_DB_TABLE_PREFIX=kdms_
```

**Important: make sure that all the required parameters are inserted into the `.env` file.**

> if you are developing the K-Box, you could generate an application key with `php artisan key:generate`

## Dynamic Configuration

Dynamic configuration is stored in the `options` table inside the database. 

The dynamic configuration is intended for configuration parameters that may vary during 
the normal execution of the K-Box.

The following table lists the option key, the data type, default value and the purpose of the setting.
During the K-Box usage the options are saved by specific administration pages.

| option key                    | type    | default | description   |
|-------------------------------|---------|---------|---------------|
| `mail.host`                   | string  |         | The host address of the SMTP server |
| `mail.port`                   | integer | 587     | The SMTP server port |
| `mail.encryption`             | string  | tls     | Only TLS encrypcted mail servers are supported |
| `mail.username`               | string  |         | The SMTP server username for authentication |
| `mail.password `              | string  |         | The password required by your SMTP server |
| `mail.from.address`           | string  |         | The email address used as the sender of your emails | 
| `mail.from.name`              | string  |         | The name that is associated to the senders email address|
| `public_core_enabled`         | boolean | false   | If the connection to the public network is enabled | 
| `public_core_url`             | string  |         | The URL of the K-Link Public Network |
| `public_core_username`        | string  |         | The Network authentication username |
| `public_core_password`        | string  |         | The Network authentication password |
| `public_core_debug`           | boolean | false   | Activate the debug of the K-Link connection |
| `public_core_correct`         | boolean | false   | If the Network configuration is correct |
| `public_core_network_name_en` | string  |         | The English name of the Network |
| `public_core_network_name_ru` | string  |         | The Russian name of the Network |
| `support_token`               | string  |         | The token for the support widget |
| `analytics_token`             | string  |         | The token for the Analytics service |


## Docker image configuration

The K-Box Docker image has specific configuration options, expressed via environment variables

| parameter                      | required | type    | default value   | description |
|--------------------------------|----------|---------|-----------------|-------------|
| `KBOX_APP_URL`                 | ✓        | url     |                 | The url on which the K-Box will be reachable, e.g. https://my.kbox.tld/ |
| `KBOX_APP_LOCAL_URL`           | ✓        | url     |                 | The URL on which the K-Box listen for file download request issued by the K-Search |
| `KBOX_APP_KEY`                 |          | string  | _autogenerated_ | Encryption Key. See [Application Key section](#application-key) |
| `KBOX_APP_ENV`                 |          | string  | production      | the environment under which the application is running, e.g. local, production |
| `KBOX_APP_DEBUG`               |          | boolean | false           | Activate the debug mode |
| `KBOX_ADMIN_USERNAME`          |          | string  |                 | The email address, i.e. username, of the default administrator account |
| `KBOX_ADMIN_PASSWORD`          |          | string  |                 | The password of the default administrator account |
| `KBOX_UPLOAD_LIMIT`            |          | number  | 204800          | The maximum file upload size, in KB |
| `KBOX_FLAGS`                   |          | string  |                 | The space separated list of [flags](./flags.md) to enable |
| `KBOX_LOAD_PRIVACY`            |          | boolean | false           | Set it to true to trigger the privacy policy creation from templates |

> In addition to these specific variables, all static configuration variables prefixed with `KBOX_` can also be configured as part of the Docker environment variables

**PHP specific configuration**

| parameter                      | required | type    | default value   | description |
|--------------------------------|----------|---------|-----------------|-------------|
| `KBOX_PHP_MAX_EXECUTION_TIME`  |          | number  | 120             | See [PHP max-execution-time](http://php.net/manual/en/info.configuration.php#ini.max-execution-time) |
| `KBOX_PHP_MAX_INPUT_TIME`      |          | number  | 120             | See [PHP max-input-time](http://php.net/manual/en/info.configuration.php#ini.max-input-time) |
| `KBOX_PHP_MEMORY_LIMIT`        |          | string  | 500M            | See [PHP memory-limit](http://php.net/manual/en/ini.core.php#ini.memory-limit) |
| `KBOX_PHP_POST_MAX_SIZE`       |          | string  | 120M            | See [PHP post-max-size](http://php.net/manual/en/ini.core.php#ini.post-max-size) |
| `KBOX_PHP_UPLOAD_MAX_FILESIZE` |          | string  | 100M            | See [PHP upload-max-filesize](http://php.net/manual/en/ini.core.php#ini.upload-max-filesize) |

> Setting the `KBOX_UPLOAD_LIMIT` will set the `KBOX_PHP_POST_MAX_SIZE` and `KBOX_PHP_UPLOAD_MAX_FILESIZE`, if not already defined

### Options that cannot be configured in the Docker image

The resumable upload environment variables, that are available in the static configuration, cannot be configured when running the K-Box using the Docker image.

- `TUSUPLOAD_USE_PROXY`
- `TUSUPLOAD_HOST`
- `TUSUPLOAD_HTTP_PATH`
- `TUSUPLOAD_URL`

## Application Key

The Application Key is a 32 characters long string that is used by the application to ensure that encrypted strings are safe.

This string can be set at the environment level, using the `APP_KEY` environment variable, or with `KBOX_APP_KEY` if the Docker image is used. 
Leaving the environment variable empty, or with a string shorter than 32 characters, will cause the generation of a new application key. 

The Docker image is able to automatically generate and store the application key in `storage/app/app_key.key`, if a value is omitted.

> Changing Application Key between deployments will invalidate all user sessions

> **Breaking change** 16 characters application key are not anymore supported


## First Administrator

The First Administrator is the user that completes the setup and is able to fully manage the K-Box. It can also create other administrators.

By default the K-Box do not create an administrator account automatically. 

The preferred way is to use the `create-admin` command

```bash
php artisan create-admin {email}
## or
docker-compose exec kbox php artisan create-admin {email}
## if executed from Docker
```

The create-admin requires an email address, that will be used as the username and will ask for a password in interactive mode.
Inserting an empty password, or running with the option `--no-interaction`, will cause the command to generate and print a password reset link. The link is valid for 5 minutes and, opening it, will enable you to define your own password via User Interface.

As an alternative, and for backward compatibility, the administrator account can be created by setting 
the `KLINK_DMS_ADMIN_USERNAME` and `KLINK_DMS_ADMIN_PASSWORD` environment variables inside the Docker configuration.

```yaml
KBOX_ADMIN_USERNAME: "admin@kbox.local"
KBOX_ADMIN_PASSWORD: "*******"
```

## Environment variable name changes

The next table summarizes the changes to the environment variable applied in version 0.22 of the K-Box.

> To not break backward compatibility the 0.21 variable names are still supported

| version 0.21 and below                | new name from version 0.22         |
|---------------------------------------|------------------------------------|
| `KLINK_DMS_APP_URL`                   | `KBOX_APP_URL`                     |
| `KLINK_DMS_APP_INTERNAL_URL`          | `KBOX_APP_LOCAL_URL`               |
| `KLINK_DMS_APP_KEY`                   | `KBOX_APP_KEY`                     |
| `KLINK_DMS_APP_ENV`                   | `KBOX_APP_ENV`                     |
| `KLINK_DMS_APP_DEBUG`                 | `KBOX_APP_DEBUG`                   |
| `KLINK_DMS_ADMIN_USERNAME`            | `KBOX_ADMIN_USERNAME`              |
| `KLINK_DMS_ADMIN_PASSWORD`            | `KBOX_ADMIN_PASSWORD`              |
| `KLINK_DMS_CORE_ADDRESS`              | `KBOX_SEARCH_SERVICE_URL`          |
| `KLINK_DMS_DB_NAME`                   | `KBOX_DB_NAME`                     |
| `KLINK_DMS_DB_HOST`                   | `KBOX_DB_HOST`                     |
| `KLINK_DMS_DB_USERNAME`               | `KBOX_DB_USERNAME`                 |
| `KLINK_DMS_DB_PASSWORD`               | `KBOX_DB_PASSWORD`                 |
| `KLINK_DMS_DB_TABLE_PREFIX`           | `KBOX_DB_TABLE_PREFIX`             |
| `KLINK_DMS_MAX_UPLOAD_SIZE`           | `KBOX_UPLOAD_LIMIT`                |
| `KLINK_PHP_POST_MAX_SIZE`             | `KBOX_PHP_POST_MAX_SIZE`           |
| `KLINK_PHP_UPLOAD_MAX_FILESIZE`       | `KBOX_PHP_UPLOAD_MAX_FILESIZE`     |
| `KLINK_PHP_MEMORY_LIMIT`              | `KBOX_PHP_MEMORY_LIMIT`            |
| `DMS_ITEMS_PER_PAGE`                  | `KBOX_PAGE_LIMIT`                  |
| `MAIL_DRIVER`                         | `KBOX_MAIL_DRIVER`                 |
| `MAIL_HOST`                           | `KBOX_MAIL_HOST`                   |
| `MAIL_PORT`                           | `KBOX_MAIL_PORT`                   |
| `MAIL_FROM_ADDRESS`                   | `KBOX_MAIL_FROM_ADDRESS`           |
| `MAIL_FROM_NAME`                      | `KBOX_MAIL_FROM_NAME`              |
| `MAIL_ENCRYPTION`                     | `KBOX_MAIL_ENCRYPTION`             |
| `MAIL_USERNAME`                       | `KBOX_MAIL_USERNAME`               |
| `MAIL_PASSWORD`                       | `KBOX_MAIL_PASSWORD`               |
| `DMS_ARE_GUEST_PUBLIC_SEARCH_ENABLED` | `KBOX_ENABLE_GUEST_NETWORK_SEARCH` |

### Deprecation

The following environment variables are deprecated:

- `KLINK_DMS_DIR`
- `DMS_USE_HTTPS` is now deprecated and should not be set manually
- `DMS_INSTITUTION_IDENTIFIER` is now deprecated, and as part of the Institution management removal will be removed in a future version
- `DMS_IDENTIFIER` is now deprecated. The variable will be ignored if set
- `KLINK_SETUP_WWWUSER` is now deprecated, the default user will be `www-data`
- `DMS_ENABLE_ACTIVITY_TRACKING` was not used, so it has been deprecated. The variable will be ignored if set
- `DMS_UPLOAD_FOLDER` was not used, so it has been deprecated
- `DMS_RECENT_TIMELIMIT` is now deprecated as it was not used

### Changes

- `KBOX_APP_LOCAL_URL` default value, if used from Docker, is now `http://kbox/`. The new default value follow the example docker compose file
- `KBOX_PHP_POST_MAX_SIZE` and `KBOX_PHP_UPLOAD_MAX_FILESIZE` are automatically set based on the `KBOX_UPLOAD_LIMIT` value
