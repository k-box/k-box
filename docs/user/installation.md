#### Previous: [Requirements](./requirements.md)
---
Title: Installation
Description: How to install the K-Box
---

# Installation

The K-Box and it's components are available as a [Docker](https://www.docker.com/) images.

This guide will walk you through the installation and configuration of a K-Box instance on a Linux based OS.

## Prerequisites

- meet the [hardware requirements](./requirements.md);
- Use an operating system [supported by Docker](https://docs.docker.com/install/#server) (we recommend GNU/Linux; we use [Debian](https://debian.org) 9);
- [Docker](https://docs.docker.com/install/linux/docker-ce/debian/) and [Docker Compose](https://docs.docker.com/compose/install/) installed.

## Installation

### Environment

First create a directory, which will contain all files needed for the installation. For example, we use `k-box`.

In this directory we are going to create a configuration file with the name `docker-compose.yml`. It will contain the specific setup, and the sub-folders to hold all the data. For an easy start, the K-Box code comes with an [example file](../../docker-compose.example.yml), which already includes all required services. You can just copy it over and rename it:

```bash
curl -o docker-compose.yml https://raw.githubusercontent.com/k-box/k-box/master/docker-compose.example.yml
```

Within this docker compose file, there are four services defined:

1. `kbox`, the web application of the K-Box
2. `database`, the MariaDB database for the K-Box
3. `ksearch`, the [K-Search](https://github.com/k-box/k-search) API;
4. `engine`, the [K-Search Engine](https://github.com/k-box/k-search-engine) based on Apache SOLR.

Each service indicate which Docker image to use and some basic environment variables.

**Please note:** Per default configuration, the data is saved inside the same directory you located the `docker-compose.yml`, inside a directory called `storage`.

### Configuration

The example `docker-compose.yml` file contains already suitable defaults for most of the configuration. When running K-Link in the wild (Internet) make sure you adjust at least the following variables:

- The domain the K-Box is running: `KLINK_DMS_APP_URL`
- Admin user and password for the K-Box: `KLINK_DMS_ADMIN_USERNAME`, `KLINK_DMS_ADMIN_PASSWORD`
- Alter the used database passwords: `MYSQL_ROOT_PASSWORD`,`MYSQL_PASSWORD`,`DATABASE_PASSWORD`

Learn more about the [deployment configuration](./deploy-configuration.md).

### Download and start

Once the configuration file has been saved, you can make Docker to download the required images and start up the services.

Just execute in your directory:

```bash
docker-compose up --detach
```

_Running this for the first time, this step will download quite a lot of data and might take a while._

Afterwards K-Box will be available: [http://localhost:8080](http://localhost:8080/)

### Create the administrator

The default deployment configuration do not specify a default administrator account.

You can create an administrator account after the K-Box start-up using:

```bash
docker-compose exec kbox php artisan create-admin {email}
```

This command will ask for the password and generate the account.

> If you want you can still [configure the default account using deploy configuration](./deploy-configuration.md#k-box-administrator)

> Using a real email address, for `{email}` is encouraged

### Useful commands

There are some handy commands you can use to manage your K-Box:

| Function | Command |
|----------|---------|
| Start K-Box | `docker-compose up --detach` |
| Stop K-Box | `docker-compose stop` |
| Check status of running instances | `docker-compose ps` |
| See logs | `docker-compose logs --follow` |

**Please note**: Make sure you execute all these commands inside the directory you placed the `docker-compose.yml` file.

For more information see complete [documentation on Docker Compose](https://docs.docker.com/compose/reference/up/).

#### Next: [Deploy configuration](./deploy-configuration.md)
