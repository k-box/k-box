---
Title: Deployment Configuration
Description: What can be configured of the K-Box at deploy time
---

The K-Box offers various configuration options at deployment time.

| parameter                      | required     | type    | default value | description |
|--------------------------------|--------------| --------|---------------|-------------|
| `APP_ENV`                      | **required** | string  |               | the environment (when developing use local) |
| `APP_DEBUG`                    |              | boolean | false         | Set to true will enable debug, false will prevent debug information to show up |
| `APP_KEY`                      | **required** | string  |               | Encryption Key. This key is used by the Illuminate encrypter service and should be set to a random, 32 character string, otherwise the encrypted strings will not be safe. **Please do this before deploying an application!**. **Please do not change during the application execution otherwise you have to save again everything that is encrypted** |
| `APP_URL`                      | **required** | string  |               | The url of the public folder, if you use a virtual host insert the virtual host url here, e.g. https://test.klink.asia/dms/ |
| `DB_USERNAME`                  | **required** | string  | dms           | The database user that has only priviledge over the database specified by `DB_NAME` |
| `DB_PASSWORD`                  | **required** | string  |               | The database user password |
| `DB_HOST`                      |              | string  | localhost     | The database sever host |
| `DB_NAME`                      | **required** | string  | dms           | The Database name |
| `DB_TABLE_PREFIX`              | **required** | string  | kdms_         | The table prefix for each database table. The default value is just an example, to increse the security of the installation set you own value |
| `DMS_INSTITUTION_IDENTIFIER`   | **required** | string  |               | The institution identifier that is required for communicating with the K-Link Core |
| `DMS_CORE_ADDRESS`             | **required** | url     |               | The URL of the K-Link Core that will be used by the DMS. |
| `DMS_CORE_USERNAME`            | **required** | string  |               | The username for authenticating on the core. |
| `DMS_CORE_PASSWORD`            | **required** | string  |               | The password for authenticating on the core. |
| `DMS_IDENTIFIER`               | **required** | string  |               | The unique identifier for the DMS instance |
| `DMS_MAX_UPLOAD_SIZE`          |              | integer | 30000         | The maximum size of the file allowed for upload in kilobytes |
| `MAIL_ENCRYPTION`              |              | string  | tls           | The mail encryption that should be used. If set to an empty string, insecure connections will be allowed (do this for testing purposes only). |

