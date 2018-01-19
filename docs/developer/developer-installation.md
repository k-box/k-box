# Contributing to the K-Box development

FIRST RUN, tl;dr

requirements

- a running mysql/mariadb
- a running K-Search
- PHP 7.0+
- NodeJS 6.x+

```bash
npm install
composer install --prefer-dist
composer run install-video-cli
chmod +x ./bin/bin/packager-linux

php artisan clear-compiled

## create env file for configuration

## Make sure the storage folder has write permission

php artisan dms:update --no-test
php artisan dms:create-admin admin@klink.local password

npm run dev

php artisan dms:lang-publish

php artisan dms:queuelisten # for the queue runner, this is a blocking call

php artisan serve # for starting the PHP integrated web server, this is a blocking call
```

## Build and Run

If you want to understand how the K-Box works, debug an issue or contribute you can download and build the K-Box locally.

### Getting the sources

**Note**: on Windows you might want to set `core.autocrlf=false` and `core.safecrlf=true`, to keep the line endings we have in our source files.

```bash
git clone https://github.com/k-box/k-box.git
```

### Prerequisites

- [Git](https://git-scm.com/)
- [PHP](https://php.net/), `>= 7.0.21, < 7.1.0`
- [Composer](https://getcomposer.org/download/)
- [Node.JS](https://nodejs.org/en/), `>= 8.9.1, < 9.0.0`
- [Yarn](https://yarnpkg.com/en/), follow the [installation guide](https://yarnpkg.com/en/docs/install)
- [Mysql](https://www.mysql.com/) `>= 5.7` or MariaDB

Finally, install all dependencies using `Composer` and `Yarn`:

```bash
composer install

yarn
```

### Build

From a terminal, where you have cloned the `k-box` repository, execute the following command to run the packaging of the required modules

```bash
yarn run development
```

### Configuration

The environment configuration uses a `.env` file located in the root folder. 
The `.env` file contains all the sensible configuration (local url, tokens, password, etc.).

The available configuration parameters are discussed in the [Configuration Section](./configuration#static-configuration).

**Important: make sure that all the required parameters are inserted into the `.env` file.**

**It's important that the parameters DMS_INSTITUTION_IDENTIFIER, DMS_CORE_ADDRESS, DMS_CORE_USERNAME, DMS_CORE_PASSWORD, DMS_IDENTIFIER are configured in the `.env` file before proceeding** (contact your K-Link Development team referent to get the values for those parameters).

Just to verify that at least the PHP dependencies and the environment is configured correctly execute the following command

	> php artisan

The ouput must be the list of invocable artisan commands.

### 4. Testing the K-Link parameters

Before proceeding to the next configuration steps is highly important that the K-Link Core configuration is verified. To ensure that run from the command line

	> php artisan dms:test

This command will output a success message if the parameter are configured correctly. An error with the corresponding description will be printed in case of failure.

### 5. Database creation and seeding

After configuring the environment parameters you can create the database and seed the default values.

The creation and seeding operation are performed by lauching the `dms:update` Laravel Artisan command:

```bash
php artisan dms:update
```

This command will take care of installing a fresh version of the database if there is no existing DMS installation. In case an existing installation is found it will be upgraded (if needed). 
Please note that the update command performs, also, the connection test to the K-Link Core. If you don't want to perform it and you are installing the Project Edition of the DMS you can pass the `--no-test` option to skip the K-Link Core testing.

Now that the database is correctly setup the default administration user can be created by issuing the following command
```bash
php artisan dms:create-admin user_email user_password [user_nickname]
```
Replace `user_email` with a valid email address and `user_password` with an 8 character (minimum) long password.



### 6. Test if is all up

Please be sure that the following folders have write permissions:

- `storage/logs`;
- `storage/framework`;
