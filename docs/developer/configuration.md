# DMS Configuration

The DMS configuration parameters are divided into two categories: static and dynamic.


## Static Configuration

The static configuration is determined by the environment configuration file. The 
environment configuration is usually stored in the `.evn` file that is placed in the 
root of the project. An example environment file is in `env.example`.

All the environment options refers to configuration properties contained in the 
`config` folder files.

The following table shows the DMS specific configuration parameters and the 
one that are strictly required in the environment file.

| parameter                             | required     | type    | default value | description |
|---------------------------------------|--------------| --------|---------------|-------------|
| `APP_ENV`                             | **required** | string  |               | the environment (when developing is highly encoraged to use local) |
| `APP_DEBUG`                           |              | boolean | false         | Set to true will enable debug, false will prevent debug information to show up |
| [`APP_KEY`](#application-key)                             | **required** | string  |               | Encryption Key. This key is used to make encrypted strings safe. Must be set to a random 32 character string |
| `APP_URL`                             | **required** | string  |               | The url of the public folder, if you use a virtual host insert the virtual host url here, e.g. https://test.klink.asia/dms/ |
| `DB_USERNAME`                         | **required** | string  | dms           | The database user that has only priviledge over the database specified by `DB_NAME` |
| `DB_PASSWORD`                         | **required** | string  |               | The database user password |
| `DB_HOST`                             |              | string  | localhost     | The database sever host |
| `DB_NAME`                             | **required** | string  | dms           | The Database name |
| `DB_TABLE_PREFIX`                     | **required** | string  | kdms_         | The table prefix for each database table. The default value is just an example, to increse the security of the installation set you own value |
| `DMS_INSTITUTION_IDENTIFIER`          | **required** | string  |               | The institution identifier that is required for communicating with the K-Link Core |
| `DMS_CORE_ADDRESS`                    | **required** | url     |               | The URL of the K-Link Core that will be used by the DMS. |
| `DMS_CORE_USERNAME`                   | **required** | string  |               | The username for authenticating on the core. |
| `DMS_CORE_PASSWORD`                   | **required** | string  |               | The password for authenticating on the core. |
| `DMS_IDENTIFIER`                      | **required** | string  |               | The unique identifier for the DMS instance |
| `DMS_ARE_GUEST_PUBLIC_SEARCH_ENABLED` |              | boolean |               | Tell if the DMS will allow guest user to perform public search over K-Link instance |
| `DMS_MAX_UPLOAD_SIZE`                 |              | integer | 30000         | The maximum size of the file allowed for upload in kilobytes |
| `MAIL_ENCRYPTION`                     |              | string  | tls           | The mail encryption that should be used. If set to an empty string, insecure connections will be allowed (do this for testing purposes only). |

The following block is a non exhaustive example of a `.env` file.

```
APP_ENV=local 
APP_DEBUG=false
APP_KEY=%RANDOM_STRING%
APP_URL=http://localhost/dms/
DB_USERNAME=dms
DB_PASSWORD=&middot;&middot;&middot;&middot;
DB_HOST=localhost
DB_NAME=dms
DB_TABLE_PREFIX=kdms_
```

**Important: make sure that all the required parameters are inserted into the `.env` file.**

**Please check that the parameter `DMS_INSTITUTION_IDENTIFIER`, `DMS_CORE_ADDRESS`, `DMS_CORE_USERNAME`, `DMS_CORE_PASSWORD`, `DMS_IDENTIFIER` are configured in the `.env` file before proceeding** 
(contact your K-Link Development team referent to get the values for those parameters).

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

## Flags

Some features that are in testing might be behind activation flags.

Feature flags are stored in the `options` table with the `flag_` prefix. 
The `KBox\Flags` is the class responsible to interact with the flags. All flags are boolean

| flag          | description                        |
|---------------|------------------------------------|
| unifiedsearch | Control the Unified search feature |



## Application Key

The Application Key is a 32 characters long string that is used by the application to ensure that encrypted strings are safe.

This string can be set at the environment level, using the `KLINK_DMS_APP_KEY` environment variable. 
Leaving the environment variable empty, or with a string shorter than 32 characters, will cause the generation of a new application key. 

The generated application key is stored in `storage/app/app_key.key` for future startup.

> Changing Application Key between deployments will invalidate all user sessions

> **Breaking change** 16 characters application key are not anymore supported


## First Administrator

The First Administrator is the user that completes the setup and is able to fully manage the K-Box. It can also create other administrators.

By default the K-Box do not create an administrator account automatically. 

The preferred way is to use the `create-admin` command

```bash
php artisan create-admin {email}

# docker-compose exec kbox php artisan create-admin {email} if executed from Docker
```

The create-admin requires an email address, that will be used as the username and will ask for a password in interactive mode.
Inserting an empty password, or running with the option `--no-interaction`, will cause the command to generate and print a 
password reset link. The link is valid for 5 minutes and, opening it, will enable you to define your own password 
via User Interface.

As an alternative, and for backward compatibility, the administrator account can be created by setting 
the `KLINK_DMS_ADMIN_USERNAME` and `KLINK_DMS_ADMIN_PASSWORD` environment variables inside the Docker configuration.

```yaml
KLINK_DMS_ADMIN_USERNAME: "admin@kbox.local"
KLINK_DMS_ADMIN_PASSWORD: "*******"
```
