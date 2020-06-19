# K-Box development

## Prerequisites

- [Git](https://git-scm.com/)
- [PHP](https://php.net/), `>= 7.2`
- [Composer](https://getcomposer.org/download/)
- [Node.JS](https://nodejs.org/en/), `>= 12`
- [Yarn](https://yarnpkg.com/en/), follow the [installation guide](https://yarnpkg.com/en/docs/install)

> This guide assume that _git_, _php_, _composer_ and _yarn_ are directly available on your development machine

In addition the following services should be reachable

- [MariaDB](https://mariadb.org/) `>= 10.3` or [Mysql](https://www.mysql.com/) `>= 5.7`
- [K-Search](https://github.com/k-box/k-search) configured to run locally without authentication

> if you don't want to install them separately you can run them using Docker, as explained in the [services](#required-services) section 

## Getting the sources

The source code is available in a git repository hosted on GitHub. You can obtain the code by cloning it:

```bash
git clone https://github.com/k-box/k-box.git
```

> If you want to contribute, please review also the [contribution guidelines](../../contributing.md)

> on Windows you might want to set `core.autocrlf=false` and `core.safecrlf=true`, to keep the line endings we have in our source files.


## Dependencies installation

Once the repository is cloned, the required PHP and javascript dependencies can be installed. We do so by issuing:

```bash
composer install --prefer-dist

yarn
```

**Additional dependencies**

The K-Box have additional dependencies, e.g. FFMpeg, the language guesser or text extractors from PDF, that are defined by some of the PHP dependencies defined in the `composer.json` file. Composer will not execute installation scripts exposed by dependent packages, therefore we need to run them manually:

```bash    
composer run install-video-cli
composer run install-content-cli
composer run install-language-cli
composer run install-streaming-client
```

> Depending on your operating system, make sure that binaries in the `/bin` folder are executable

> If there are problems, check the [Troubleshooting section](#troubleshooting)

**Cleaning**

At the end of the installation, some files might have been compiled for production. 
These files include class loading optimization and cache of configuration files. 
To prevent that configuration caches slow down your development, execute

```bash
php artisan clear-compiled
php artisan config:clear
php artisan route:clear
```

### Required services

The K-Box requires a MySql database and a running K-Search instance. 

You can install those services from source, via prebuilt packages or via Docker.

For this documentation we consider to start those services using Docker.

An example docker compose file is available in `docker-compose.dev.example.yml`. It defines

- 2 MariaDB instances, one configured for main use and one for unit tests
- a K-Search with its related K-Search Engine

```bash
# copy the example configuration
cp docker-compose.dev.example.yml docker-compose.yml

# pull the images and start the containers
docker-compose pull
docker-compose up -d
```

### Environment configuration

The environment configuration uses a `.env` file located in the root folder. The environment file define the deploy configuration and contains all the sensible information (local url, tokens, password, etc.).

The project root folder contain a example environment configuration in the file `env.example`.

You can copy it to be your environment file, `cp env.example .env` and then customize it.

**Generate an Application Key**

The application key is required to generate secure encrypted values. Generate a new one using:

```bash
php artisan key:generate
```

**Application URL**

The application URL defines the location on which the K-Box will be exposed. The example configuration uses `localhost:8000` as the URL on which the K-Box will be listening

```conf
APP_URL=http://localhost:8000
```

> Even if the K-Box is a web application, it requires the configuration of the application public URL in order to use it while handling asynchrounous processes, e.g. sending emails.

**Set the URL that the K-Search will use to pull files for indexing**

The K-Search pulls the data from the the K-Box upon indexing request.

The default configuration is suitable for Windows with a Docker based deployment. The configuration expects the K-Box to be running on localhost and on port 8000.

```conf
APP_INTERNAL_URL=http://docker.for.win.localhost:8000/
```

If your are on Linux, where Docker is native, you can comment (or delete) the `APP_INTERNAL_URL`, as the `APP_URL` will be used.

> Since Docker for Windows and for Mac uses a virtual machine, the containers cannot resolve `localhost`. For them Docker defines `docker.for.win.localhost` and `docker.for.mac.localhost` respectively.

**Mail configuration**

The suggested development email configuration uses the log driver. 
All emails will be written in plain text inside the [application log](../user/maintenance/logging.md).

If you want to configure a real SMPT server you can comment or remove the following line from the `.env` file

```conf
MAIL_DRIVER=log
```

> The whole list of available configuration parameters are discussed in the [Configuration Section](./configuration#static-configuration).


In order to verify that the PHP dependencies and the environment is configured correctly execute the following command

```bash
php artisan
```

The ouput must be the list of available [command line tools](./commands/index.md).

## Frontend build

Before starting the K-Box, the frontend CSS and JS must be built. The repository do not contain pre-built versions of it to reduce merge conflict while submitting Pull requests that heavily change the frontend styles.

```bash
# Build the CSS and JS
yarn development

# Generate the language files for Javascript
php artisan dms:lang-publish
```

## Installation

Now that the environment is configured, we can setup the database, seed the default values and the create the administrator user.

The creation and seeding operation are performed by lauching the `dms:update` Laravel Artisan command:

```bash
php artisan dms:update --no-test
```

### First user creation

Now that the database is correctly setup the default administration user can be created by issuing the command

```bash
php artisan create-admin user_email
```
Replace `user_email` with a valid email address. An 8 character password will be asked during the command execution.

## Running

Everything is now ready for the startup. We will use the PHP integrated webserver to serve the application

```bash
php artisan serve
```

> This is a blocking command, to stop it press `Ctrl+C`

In addition, document processing and email sending, requires the asynchronous job runner to be active

```bash
php artisan dms:queuelisten
```

> This is a blocking command, to stop it press `Ctrl+C`

## Troubleshooting

### Composer install fails, `fileinfo` extension not found

Some packages require specific PHP extensions that might not be active by default. This is especially true on PHP for Windows.

Verify that the `fileinfo` extension is loaded in your php configuration file.

### Video processing CLI installation failure

The automatic download of FFmpeg might fail. You can manually obtain the files for your Operating System using the following links

- Windows, https://ffmpeg.zeranoe.com/builds/win64/static/ffmpeg-3.3.4-win64-static.zip
- Linux, https://johnvansickle.com/ffmpeg/releases/ffmpeg-3.3.4-64bit-static.tar.xz 
- MacOS, https://ffmpeg.zeranoe.com/builds/macos64/static/ffmpeg-3.3.4-macos64-static.zip

After downloading, unzip the following binaries and put them in the `/bin/bin` folder

- ffmpeg
- ffprobe


### Errors when writing files

Please make sure that the following folders have write permissions:

- `storage/logs`;
- `storage/framework`;
