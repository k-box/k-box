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
- Admin user and password for the K-Box (`KBOX_ADMIN_USERNAME`, `KBOX_ADMIN_PASSWORD`);
- The domain the K-Box is running: (`KBOX_APP_URL`).

The default configuration, contained in the `docker-compose.yml` file, exposes the K-Box on localhost, without https and on port `8000`.

### Database

The `database` requires two passwords, the first is the root password and the second is the user password for accessing the specific new database.

```yaml
MYSQL_ROOT_PASSWORD: "2381aa6a99bee6ff61c2209ef4373887"
MYSQL_PASSWORD: "b2510859c83414e0cbefd26284b9171d"
```

The `MYSQL_PASSWORD` password must be copied in the `kbox` service configuration as `KBOX_DB_PASSWORD`

```yaml
KBOX_DB_PASSWORD: "b2510859c83414e0cbefd26284b9171d"
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
KBOX_APP_URL: "https://my.box.tld/"
```

> if the application will be available on https by default, please remove the `DMS_USE_HTTPS: "false"` line from the `docker-compose.yml` file


## Further configuration options

The K-Box offers various configuration options at deployment time and runtime. Please refer to [](../developer/configuration.md)
