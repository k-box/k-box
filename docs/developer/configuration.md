# K-Box Configuration

The K-Box configuration parameters are divided into two categories: static and dynamic.

Static configuration refer to settings expressed at deploy time. Those settings cannot 
be edited from the K-Box while is running.
Dynamic configuration refer to options that can be expressed at deploy time and edited 
from the user interface, or entirely configurable from the user interface.

## Static Configuration

The static configuration is determined by the environment configuration file. It is 
usually stored in the `.evn` file, placed in the root of the project. 
An example environment file is in `env.example`.

The next table shows the K-Box specific configuration parameters:

| parameter                             | required     | type    | default value | description |
|---------------------------------------|--------------| --------|---------------|-------------|
| `APP_ENV`                             | **required** | string  |               | the environment (when developing is highly encoraged to use local) |
| `APP_DEBUG`                           |              | boolean | false         | Set to true will enable debug, false will prevent debug information to show up |
| [`APP_KEY`](#application-key)                             | **required** | string  |               | Encryption Key. This key is used to make encrypted strings safe. Must be set to a random 32 character string |
| `APP_URL`                             | **required** | string  |               | The url of the public folder, if you use a virtual host insert the virtual host url here, e.g. https://test.klink.asia/dms/ |
| `KBOX_DB_USERNAME` (`DB_USERNAME`)                         | **required** | string  | dms           | The database user that has only priviledge over the database specified by `DB_NAME` |
| `KBOX_DB_PASSWORD` (`DB_PASSWORD`)                         | **required** | string  |               | The database user password |
| `KBOX_DB_HOST` (`DB_HOST`)                             |              | string  | localhost     | The database sever host |
| `KBOX_DB_NAME` (`DB_NAME`)                             | **required** | string  | dms           | The Database name |
| `KBOX_DB_TABLE_PREFIX` (`DB_TABLE_PREFIX`)                     | **required** | string  | kdms_         | The table prefix for each database table. The default value is just an example, to increse the security of the installation set you own value |
| `DMS_INSTITUTION_IDENTIFIER`          | **required** | string  |               | The institution identifier that is required for communicating with the K-Link Core |
| `DMS_CORE_ADDRESS`                    | **required** | url     |               | The URL of the K-Link Core that will be used by the DMS. |
| `DMS_ARE_GUEST_PUBLIC_SEARCH_ENABLED` |              | boolean |               | Tell if the DMS will allow guest user to perform public search over K-Link instance |
| `DMS_MAX_UPLOAD_SIZE`                 |              | integer | 30000         | The maximum size of the file allowed for upload in kilobytes |
| `MAIL_ENCRYPTION`                     |              | string  | tls           | The mail encryption that should be used. If set to an empty string, insecure connections will be allowed (do this for testing purposes only). |

`KBOX_MAIL_DRIVER`
`KBOX_MAIL_HOST`
`KBOX_MAIL_PORT`
`KBOX_MAIL_FROM_ADDRESS`
`KBOX_MAIL_FROM_NAME`
`KBOX_MAIL_USERNAME`
`KBOX_MAIL_PASSWORD`

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
the normal execution of the K-DMS.

| option key                  | type    | default value | description   |
|-----------------------------|---------|---------------|---------------|
| mail.host                   | string  |               | Here you may provide the host address of the SMTP server used by your applications. |
| mail.port                   | integer | 587           | This is the SMTP port used by your application to deliver e-mails to users of the application. |
| mail.encryption             | string  | tls           | Only TLS encrypcted mail servers are supported |
| mail.username               | string  |               | If your SMTP server requires a username for authentication, you should set it here. This will get used to authenticate with your server on connection. |
| mail.password               | string  |               | Here you may set the password required by your SMTP server to send out messages from your application. This will be given to the server on connection so that the application will be able to send messages. |
| mail.from.address           | string  |               | You may wish for all e-mails sent by your application to be sent from the same address. Here you can specifiy the email address used as the sender of your emails | 
| mail.from.name              | string  |               | Here you can specify the Human understandable name that is associated to the senders email address |
| mail.pretend                | boolean | true          | When this option is enabled, e-mails will not actually be sent over the web and will instead be written to your application's logs files so you may inspect the message. This is great for local development. |
| public_core_enabled         | boolean | false         | If the connection to the public network is enabled |         
| public_core_url             | string  |               | The URL of the K-Link Public Network |
| public_core_username        | string  |               | The Network authentication username |
| public_core_password        | string  |               | The Network authentication password |
| public_core_debug           | boolean | false         | If the debug flag on the network connection should be enabled |
| public_core_correct         | boolean | false         | If the Network configuration is correct |
| public_core_network_name_en | string  |               | The English name of the Network |
| public_core_network_name_ru | string  |               | The Russian name of the Network |
| support_token               | string  |               | The token for the support widget |
| analytics_token             | string  |               | The token for the Analytics service |
| map_visualization           | boolean | true          | Control the map visualization enabling and disabling |


## Docker image configuration

The K-Box Docker image has specific configuration options, expressed via environment variables

| parameter                      | required | type    | default value   | description |
|--------------------------------|----------|---------|-----------------|-------------|
| `KBOX_APP_URL`                 | v        | url     |                 |  |
| `KBOX_APP_LOCAL_URL`           | v        | url     |                 |  |
| `KBOX_APP_KEY`                 |          | string  | _autogenerated_ |  |
| `KBOX_APP_ENV`                 |          | string  | production      |  |
| `KBOX_APP_DEBUG`               |          | boolean | false           |  |
| `KBOX_ADMIN_USERNAME`          |          | string  |                 |  |
| `KBOX_ADMIN_PASSWORD`          |          | string  |                 |  |
| `KBOX_PHP_MAX_EXECUTION_TIME`  |          | number  | 120             |  |
| `KBOX_PHP_MAX_INPUT_TIME`      |          | number  | 120             |  |
| `KBOX_PHP_MEMORY_LIMIT`        |          | string  | 500M            |  |
| `KBOX_PHP_POST_MAX_SIZE`       |          | string  | 120M            |  |
| `KBOX_PHP_UPLOAD_MAX_FILESIZE` |          | string  | 100M            |  |

> In addition to these specific variables, all static configuration variables prefixed with `KBOX_` can also be configured as part of the Docker environment variables

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

The next table summarizes the changes to the environment variable that will be applied in version 0.22 of the K-Box.
To make the upgrade process smooth the previous environment variables will be still supported until version 0.26

| version 0.21 and below          | new name from version 0.22     |
|---------------------------------|--------------------------------|
| `KLINK_DMS_APP_URL`             | `KBOX_APP_URL`                 |
| `KLINK_DMS_APP_INTERNAL_URL`    | `KBOX_APP_LOCAL_URL`           |
| `KLINK_DMS_APP_KEY`             | `KBOX_APP_KEY`                 |
| `KLINK_DMS_APP_ENV`             | `KBOX_APP_ENV`                 |
| `KLINK_DMS_APP_DEBUG`           | `KBOX_APP_DEBUG`               |
| `KLINK_DMS_ADMIN_USERNAME`      | `KBOX_ADMIN_USERNAME`          |
| `KLINK_DMS_ADMIN_PASSWORD`      | `KBOX_ADMIN_PASSWORD`          |
| `KLINK_DMS_CORE_ADDRESS`        | `KBOX_SEARCH_SERVICE_URL`      |
| `KLINK_DMS_DB_NAME`             | `KBOX_DB_NAME`                 |
| `KLINK_DMS_DB_HOST`             | `KBOX_DB_HOST`                 |
| `KLINK_DMS_DB_USERNAME`         | `KBOX_DB_USERNAME`             |
| `KLINK_DMS_DB_PASSWORD`         | `KBOX_DB_PASSWORD`             |
| `KLINK_DMS_DB_TABLE_PREFIX`     | `KBOX_DB_TABLE_PREFIX`         |
| `KLINK_DMS_MAX_UPLOAD_SIZE`     | `KBOX_UPLOAD_LIMIT`            |
| `KLINK_PHP_POST_MAX_SIZE`       | `KBOX_PHP_POST_MAX_SIZE`       |
| `KLINK_PHP_UPLOAD_MAX_FILESIZE` | `KBOX_PHP_UPLOAD_MAX_FILESIZE` |
| `KLINK_PHP_MEMORY_LIMIT`        | `KBOX_PHP_MEMORY_LIMIT`        |
| `DMS_ITEMS_PER_PAGE`            | `KBOX_PAGE_LIMIT`              |
| `MAIL_DRIVER`                   | `KBOX_MAIL_DRIVER`             |
| `MAIL_HOST`                     | `KBOX_MAIL_HOST`               |
| `MAIL_PORT`                     | `KBOX_MAIL_PORT`               |
| `MAIL_FROM_ADDRESS`             | `KBOX_MAIL_FROM_ADDRESS`       |
| `MAIL_FROM_NAME`                | `KBOX_MAIL_FROM_NAME`          |
| `MAIL_ENCRYPTION`               | `KBOX_MAIL_ENCRYPTION`         |
| `MAIL_USERNAME`                 | `KBOX_MAIL_USERNAME`           |
| `MAIL_PASSWORD`                 | `KBOX_MAIL_PASSWORD`           |

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
- KBOX_PHP_POST_MAX_SIZE and KBOX_PHP_UPLOAD_MAX_FILESIZE are automatically set based on the KBOX_UPLOAD_LIMIT value
