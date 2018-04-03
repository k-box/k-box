---
Title: Installation
Description: How to install the K-Box
---

The K-Box is available as a [Docker](https://www.docker.com/) image. 

This guide will walk you through the installation and configuration of a K-Box instance on a Linux based OS.

## Prerequisites

- meet the [hardware requirements](./requirements.md)
- a clean new installation of Debian 9 (64 bit), Ubuntu or other [Docker supported OS](https://docs.docker.com/install/#server)
- [Docker](https://docs.docker.com/install/linux/docker-ce/debian/) and [Docker Compose](https://docs.docker.com/compose/install/) installed
- a properly configured DNS that resolves requests to your domain name, e.g. `my.box.tld`, is required if you want to expose the K-Box on the internet.


## Environment preparation

Once [Docker](https://docs.docker.com/install/linux/docker-ce/debian/) and [Docker Compose](https://docs.docker.com/compose/install/) are installed, the directory that will contain the installation can be created.

In the setup directory, called `~/deploy/k-box/`, we are going to create the configuration file, called `docker-compose.yml`, that contain the specific setup, and the sub-folders to contain the data.

The folder structure should look like

```
|-- deploy/k-box/
    |-- storage
        |-- data
        |-- index
        |-- database
    |-- docker-compose.yml
```

The K-Box repository offers an example [`docker-compose.yml`](https://github.com/k-box/k-box/blob/master/docker-compose.example.yml) file that includes the required services for running a K-Box.

```bash
cd deploy/k-box/
curl -o docker-compose.yml https://github.com/k-box/k-box/blob/master/docker-compose.example.yml
```

The docker compose file defines 4 services:

1. `kbox`, the web application of the K-Box
2. `database`, the MariaDB database of the K-Box
3. `ksearch`, the [K-Search API](https://github.com/k-box/k-search) layer
4. `engine`, the [K-Search Engine](https://github.com/k-box/k-search-engine) layer

Each service defines which Docker Image to use and the basic environment variables. Per default configuration the data saved in each service is not persisted on disk and uses the Docker dynamic volumes.

First, we want to make the stored document, the database and the search engine persistent accross restart and upgrades.
For each service we uncomment the `volumes` configuration

```yml
 volumes:
 - "./storage/data:/var/www/dms/storage"
```

> `./` means that the folder is at the same level as the `docker-compose.yml` file

## Configuration

The example Docker Compose file contains suitable defaults for most of the configuration, but sometimes you might want to change those defaults.
In particular is suggested to change:

- The database password
- The K-Box admin user password
- The K-Box App Key
- The K-Box URL

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

The default administrator account of the K-Box is configured at startup, the username and the password are specified in the configuration file as

```yaml
KLINK_DMS_ADMIN_USERNAME: "admin@klink.local"
KLINK_DMS_ADMIN_PASSWORD: "*******"
```

> The mimumim password length is 8 characters

### K-Box Application Key

The application key serve to secure user sessions and other encrypted data. It must be set to a 32 characters string.

```yaml
KLINK_DMS_APP_KEY: "32 characters string"
```

### K-Box URL

The K-Box needs to know the public URL that will be used to access it.

If the K-Boxes will be exposed through a secure connection, specify here the HTTPS protocol

```yaml
KLINK_DMS_APP_URL: "https://my.box.tld/"
```

> if the application will be available on https by default, please remove the `DMS_USE_HTTPS: "false"` line from the `docker-compose.yml` file

The default configuration, contained in the `docker-compose.yml` file, exposes the K-Box on localhost, without https and on port `8000`.

## First startup

Once the configuration file is saved, we can start pulling the required Docker images. We can do it with

```bash
docker-compose pull
```

These operation might take a while.

After all images are downloaded the K-Box can be started with

```bash
docker-compose up --detach
```

This will execute the [startup in detached mode](https://docs.docker.com/compose/reference/up/).

The startup process can be followed with

```bash
docker-compose ps
docker-compose logs --follow kbox
```

the first command will output the status of the containers, while the second prints (and follows) the log of the K-Box web application.

> if `docker-compose ps` shows containers terminated with `Exit 1` (or other) codes means that something in the startup failed

Once the startup process is complete, open the browser and navigate to http://localhost:8000. To login use the credentials written in the configuration.

## Next

- [First use](./first-use.md)
- [Running behind a reverse proxy](./reverse-proxy.md)
- [Startup problems](./maintenance/troubleshooting.md)
