---
Title: Deployment configuration
Description: What can be configured of the K-Box at deploy time
---

# Deployment configuration

## File system structure

The default and recommended folder structure will look like the following:

```
|-- deploy/k-box/
    |-- storage
        |-- data
        |-- index
        |-- database
    |-- docker-compose.yml
```

**Please note:** Per default configuration, the data is saved inside the same directory you located the `docker-compose.yml`, inside a directory called `storage`.

## Configuration values

The example Docker Compose file contains suitable defaults for most of the configuration, however some values are mandatory to change:

The example `docker-compose.yml` file contains already suitable defaults for most of the configuration, however some values must be adjusted before you can start:

- Define the used database passwords (`MYSQL_ROOT_PASSWORD`,`MYSQL_PASSWORD`,`DATABASE_PASSWORD`);
- Admin user and password for the K-Box (`KLINK_DMS_ADMIN_USERNAME`, `KLINK_DMS_ADMIN_PASSWORD`);
- A freely definable application key for the K-Box (`KLINK_DMS_APP_KEY`);
- The domain the K-Box is running: (`KLINK_DMS_APP_URL`).

The default configuration, contained in the `docker-compose.yml` file, exposes the K-Box on localhost, without https and on port `8000`.

### Database

The `database` requires two passwords, the first is the root password and the second is the user password for accessing the specific new database.

```yaml
MYSQL_ROOT_PASSWORD: "2381aa6a99bee6ff61c2209ef4373887"
MYSQL_PASSWORD: "b2510859c83414e0cbefd26284b9171d"
```

The `MYSQL_PASSWORD` password must be copied in the `kbox` service configuration as `KLINK_DMS_DB_PASSWORD`

```yaml
KLINK_DMS_DB_PASSWORD: "b2510859c83414e0cbefd26284b9171d"
```

### K-Box administrator

The default administrator account of the K-Box can be configured at startup.

By specifying username and the password in the configuration file, as in the next code block, the user will be automatically created.

```yaml
KLINK_DMS_ADMIN_USERNAME: "admin@kbox.local"
KLINK_DMS_ADMIN_PASSWORD: "*******"
```

> The mimumim password length is 8 characters

**errors**

User creation might fail, for example if the given username is not a valid email address, or an empty password is specified


### K-Box URL

The K-Box needs to know the public URL that will be used to access it.

If the K-Boxes will be exposed through a secure connection, specify here the HTTPS protocol

```yaml
KLINK_DMS_APP_URL: "https://my.box.tld/"
```

> if the application will be available on https by default, please remove the `DMS_USE_HTTPS: "false"` line from the `docker-compose.yml` file


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
