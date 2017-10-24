# Developer Installation

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

## Getting the code

In order to start develop on the K-Link DMS you have to configure the environment and execute the frontend build tasks. 

The repository only contain the specific DMS code, all the dependencies are managed through composer (php) and bower (js, css). The build is made using Gulp.

The commited files do not include:

- composer `vendor` folder, 
- npm `node_modules` folder,
- bower `bower_component` folder
- and the environment `.env` configuration file.

The following sections will guide you trough the prerequisites installation, environment configuration and first run of K-Link DMS.

1. OS and Web Server prerequisites
2. Development Prerequisites
3. Environment configuration
4. Testing the K-Link parameters
5. Database creation and seeding
6. Test if is all up

The setup has been tested on Ubuntu 14.04, MacOS X Yosemite (with [MAMP](http://www.mamp.info/en/)) and Windows 8.1 and 10 (with Wamp)

**A note on SSL certificates.** The Gitlab server that hosts the repositories uses a Self Signed Certificate for HTTPS, if you plan to use HTTPS to clone and push please refer to this [StackOverflow answer](http://stackoverflow.com/questions/9072376/configure-git-to-accept-a-particular-self-signed-server-certificate-for-a-partic) tells you everything you need to know (besides some command line operations the idea is valid for both Linux, Windows and MacOS). If the StackOverflow answer is not available
please refer to [this snippet](https://gitlab.klink.dyndns.ws:3000/snippets/12).


### 1. OS and Web Server prerequisites

Before proceeding you need:

- Apache 2.4
- Apache mod_rewrite active
- MariaDB 10.0 or MySQL 5.5
- PHP 5.4 (or above) and **fulfill the specific** [Laravel requirements](http://laravel.com/docs/master/installation#server-requirements). 

Just as a remark: fullfill all the Laravel specific requirements before continuing.

You can install the K-Link DMS in two ways:

1. In an Apache Alias inside in an existing virtual host
2. In a dedicated Apache Virtual Host

You can clone this repository inside the folder you want.

Consider that you have cloned this repository inside `/www/dms`, you will have the following folder structure (in this case `www` is the root folder):

	www
	|-- dms
	|    |-- app 
	|    |-- ...
	|    |-- public
	|    +-- ...

In the case described above the Apache Alias will be, for example, `/dms /www/dms/public`, while the root directory of the virtual host would be `/www/dms/public`

Just for a remark, in both cases the main directory is the `public` subfolder inside the Laravel installation directory.

If you are using an Apache Alias make sure to update the `.htaccess` file inside the `public` subfolder with the `RewriteBase` rule. As an example consider that an Alias named `dms` is configured to point to the `public` folder. To make the system aware of that alias when performing url rewrites add the following line inside the `.htaccess` file

	RewriteBase /alias

where alias is the `name` of your configured alias.

If you are using a Virtual Host based configuration make sure to check that the `.htaccess` file do not contain the `RewriteBase` rule.


### 2. Development Prerequisites

Now that the basic Apache configuration has been setup is now possible to load all the dependencies.

For managing the PHP dependencies the K-Link DMS uses [Composer](https://getcomposer.org/). Is highly encoraged to setup composer in the global path so you don't need to specify each time the path to the composer executable.
Another important thing is that the `php` command must be executable from command line.

Here is the list of tools that will be used for managing the frontend dependencies:

- [NodeJS](http://nodejs.org/) reachable from the command line
- NPM (the nodejs package manager) reachable from the command line
- [Bower](http://bower.io/) the package manager for Javascript and CSS dependencies
- [Gulp](http://gulpjs.com/) the streaming build system

----- 

For all Windows developers please configure your system with:

- PHP version 5.6 or 7.0 (both Microsoft Web Platform IIS version or WAMP version will work)
- make sure you can run `php --version` and other `php [command]` from the Command Prompt or the PowerShell
- Have MariaDB installed (at now also Mysql 5.6 that comes with WAMP should be fine)
- Install composer using the Windows Executable Installer
- Install NodeJS using the Windows Installer

---------


To install all the prerequisites execute the following commands (considering Composer and Node's 
npm command already available, and your current working directory the folder in which the DMS 
repository has been cloned): 

```bash
# let composer download the required packages
composer install --prefer-dist

# install bower (is possible that you might need to use sudo)
npm install -g bower

# install Gulp (is possible that you might need to use sudo)
npm install -g gulp

# install the build system dependencies
npm install

# download and install the frontend libraries
bower install
```

After executing all the installation of the prerequisites you can execute gulp to process the files and build the frontend needed files.
```bash
# (is possible that you might need to use sudo)
gulp
```

To verify that the build process has been performed correctly the following folders and files must exists in the `public` subfolder:

	public
	|-- build
	|    |-- css 
	|         |-- all-12345678.css *
	|    |-- js
	|         |-- vendor-12345678.css *
	|    +-- rev-manifest.js
	|-- css
	|    |-- all.css
	|    |-- app.css
	|    |-- vendor.css
	|-- images
	|-- js
	|    |-- dms.js
	|    |-- vendor.js
	|    +-- modules

All the folders must be non-empty. The file marked with * have a unique 8 character hash in the name for versioning purposes.

Next you have to create the environment configuration for you DMS installation.

### 3. Environment configuration

The environment configuration uses a `.env` file in the the DMS project root folder. The `.env` file contains all the sensible configuration (local url, tokens, password, etc.).

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
