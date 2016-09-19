# DMS Configuration

The DMS configuration parameters are divided into two categories: static and dynamic.

## Static Configuration

The static configuration refers to the environment configuration

The following table explains the configuration parameter that can (or must) be included in the `.env` file.

| parameter                             | required     | type    | default value | description |
|---------------------------------------|--------------| --------|---------------|-------------|
| `APP_ENV`                             | **required** | string  |               | the environment (when developing is highly encoraged to use local) |
| `APP_DEBUG`                           |              | boolean | false         | Set to true will enable debug, false will prevent debug information to show up |
| `APP_KEY`                             | **required** | string  |               | Encryption Key. This key is used by the Illuminate encrypter service and should be set to a random, 32 character string, otherwise the encrypted strings will not be safe. **Please do this before deploying an application!**. **Please do not change during the application execution otherwise you have to save again everything that is encrypted** |
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

## Dynamic Configuration

Dynamic configuration is stored in the `options` table inside the database. The dynamic configuration is intended for configuration parameters that may vary during the life of the DMS instance.

| option key          | type    | default value | description   |
|---------------------|---------|---------------|---------------|
| mail.host           | string  |               | Here you may provide the host address of the SMTP server used by your applications. |
| mail.port           | integer | 587           | This is the SMTP port used by your application to deliver e-mails to users of the application. |
| mail.encryption     | string  | tls           | Only TLS encrypcted mail servers are supported |
| mail.username       | string  |               | If your SMTP server requires a username for authentication, you should set it here. This will get used to authenticate with your server on connection. |
| mail.password       | string  |               | Here you may set the password required by your SMTP server to send out messages from your application. This will be given to the server on connection so that the application will be able to send messages. |
| mail.from.address   | string  |               | You may wish for all e-mails sent by your application to be sent from the same address. Here you can specifiy the email address used as the sender of your emails | 
| mail.from.name      | string  |               | Here you can specify the Human understandable name that is associated to the senders email address |
| mail.pretend        | boolean | true          | When this option is enabled, e-mails will not actually be sent over the web and will instead be written to your application's logs files so you may inspect the message. This is great for local development. |

