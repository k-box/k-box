

[![build status](https://git.klink.asia/klinkdms/dms/badges/master/build.svg)](https://git.klink.asia/klinkdms/dms/commits/master) ![latest version](https://img.shields.io/badge/version-0.13.1-blue.svg)

# K-Link DMS

The K-Link Document Managament System (K-DMS) is a web application designed 
for document management inside an Organization or in the context of a 
Project involving users of different organizations.

The K-Link DMS is built on top of [Laravel](https://laravel.com/) 5.1 (LTS).

This readme file is reserved for **developers**. For user oriented documentation 
please see the folder [`docs/user`](./docs/user/en) available in this repository.


## Setting up the development environment

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

The setup has been tested on Ubuntu 14.04 and MacOS X Yosemite (with [MAMP](http://www.mamp.info/en/))


### 1. OS and Web Server prerequisites

Before proceeding you need:

- Apache 2.4
- Apache mod_rewrite active
- MariaDB 10.0 or MySQL 5.5
- PHP 5.4 (or above) and **fulfill the specific** [Laravel requirements](http://laravel.com/docs/master/installation#server-requirements). 

Just as a remark: **fullfill all the Laravel specific requirements before continuing**.

You can install the K-Link DMS in two ways:

1. In an Apache Alias inside an existing virtual host or
2. in a dedicated Apache Virtual Host

You can clone this repository inside the folder you want.

For example, let's consider that you have cloned this repository inside `/www/dms`, you will have the following folder structure (in this case `www` is the root folder):

	www
	|-- dms
	|    |-- app 
	|    |-- ...
	|    |-- public
	|    +-- ...

In the case described above the Apache Alias will be, for example, `/dms /www/dms/public`, while the root directory of the virtual host would be `/www/dms/public`

Just for a remark, in both cases the main directory is the `public` subfolder inside the Laravel installation directory.

If you are using an Apache Alias make sure to update the `.htaccess` file (inside the `public` subfolder) with the correct `RewriteBase` rule. As an example consider that an Alias named `dms` is configured to point to the `public` folder. To make the system aware of that alias when performing url rewrites add the following line inside the `.htaccess` file

	RewriteBase /alias

where `alias` is the `name` of your configured alias.

If you are using a Virtual Host based configuration make sure to check that the `.htaccess` file do not contain the `RewriteBase` rule.


### 2. Development Prerequisites

Now that the basic Apache configuration has been setup is now possible to load all the dependencies.

For managing the PHP dependencies the K-Link DMS uses [Composer](https://getcomposer.org/). Is highly encouraged to setup composer in the global path so you don't need to specify each time the path to the composer executable.
Another important thing is that the `php` command must be executable from command line.

Before proceeding here is the list of tools that will be used for managing the frontend dependencies and you need to have on your development machine:

- [NodeJS](http://nodejs.org/) reachable from the command line
- NPM (the nodejs packets manager) reachable from the command line
- [Bower](http://bower.io/) the package manager for Javascript and CSS dependencies
- [Gulp](http://gulpjs.com/) the streaming build system

To install all the prerequisites execute the following commands (considering Composer and Node's npm command already available, and considering that your current working directory is the folder in which the DMS repository has been cloned): 
```bash
# let composer download the required packages
composer install --prefer-dist

# install bower (is possible that you might need to use sudo)
npm install bower

# install Gulp (is possible that you might need to use sudo)
npm install gulp

# install the build system dependencies
npm install

# download and install the frontend libraries
node_modules/.bin/bower install
```

After executing all the installation of the prerequisites you can execute gulp to process the files and build the frontend needed files.
```bash
node_modules/.bin/gulp
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

For the configuration parameters please refer to [developers/configuration.md](./docs/developer/configuration.md)

Just to verify that at least the PHP dependencies and the environment is configured correctly execute the following command

	> php artisan

The ouput must be the list of available commands.


### 4. Testing the K-Link parameters

Before proceeding to the next configuration steps is highly important that the K-Link Core configuration is verified. To ensure that run from the command line

	> php artisan dms:test

This command will output a success message if the parameter are configured correctly. An error with the corresponding description will be printed in case of failure.


### 5. Database creation and seeding

After configuring the environment parameters you can create the database and seed the default values.

The creation and seeding operation are performed by lauching two Laravel Artisan commands:

```bash
php artisan migrate
php artisan db:seed
```

Now that the database is correctly setup the default administration user can be created by issuing the following command
```bash
php artisan dms:create-admin
```


The output of the command will tell the username and it's password.



### 6. Test if is all up

Plase be sure that the following folders have write permissions:

- `storage/logs`;
- `storage/documents`;
- `storage/framework`;


---------------------

## DMS Command Line

The DMS offers various commands that can be executed in Command Line. 
See [`docs/developer/commands`](./docs/developer/commands/index.md) for the full list.



---------------------

## Asynchronous Jobs and Queue Runner

The DMS will use the Laravel Queue capability for handling long running jobs, like sending email, perform document import and others.

To start the asynchronous job lister execute

	php artisan dms:queuelisten

from the command line (inside the DMS folder).

This command will stay alive until is terminated (Ctrl+C or unhandled exception).

When used in production you may want to use [Supervisor](http://supervisord.org/introduction.html) to control the execution and the start of the command

The following code block is an example of a Supervisor configuration file for running the DMS queue listener.

```
[program:dms_queue]
command=/usr/bin/php /path/to/dms/artisan dms:queuelisten
autostart=true
autorestart=true
process_name=DMS-queue
stderr_logfile=/var/log/dms_queue.err.log
stdout_logfile=/var/log/dms_queue.out.log
stopsignal=TERM
```


---------------------

## Contributing

All the PHP classes must respect the PSR-4 spec and be namespaced accordingly to the Laravel rules.

The K-Link DMS application namespace is

	/KlinkDMS


The frontend files resides in the `resources` subfolder. 

The stylesheet preprocessor used is LESS. The less files are automatically compiled by gulp. To always get the latest changes when editing the style files run

	gulp watch

this will watch for file changes and execute all the frontend related tasks.


**Make sure to set your environment to development** otherwise the Workbench package used for developing Laravel packages will not be loaded.


### Javascript

Static dependencies are managed through Bower and stored in `resource/assets/vendor`, while DMS specific javascript files are in `resource\assets\js`.

Dynamic module loading is performed client side using RequireJS.

Javascript files are minified and concatenated by Gulp.

**Dependencies**

Static dependencies are:

- jQuery (~2.0.3")
- requirejs (~2.1")
- NProgress (~0.1.6")

all the dependencies are concatenated in the `public/js/vendor.js` file that is automatically generated by gulp.


To get the latest version of the scripts run

	gulp watch

Dynamic dependencies and scripts should respect the AMD specification as imposed by RequireJS.

In the normal context of execution you will have access to the following preloaded modules:

- `jquery`: The jquery library
- `DMS`: The global DMS facility


#### DMS Global Object

The DMS global object includes some helper methods to interact with the DMS services.

##### DMS.Ajax

DMS Ajax includes some simple wrapper around jQuery.ajax method that will configure the needed parameters to pass the request validation performed by the backend and to get the response in JSON format.

The DMS.Ajax has the following functions:

- `get(url, params, success, error)` perform a get request
- `post(url, params, success, error)`  perform a post request
- `put(url, params, success, error)`  perform a put request
- `delete(url, success, error)`  perform a delete request

every function returns the jqXHR object, the `params` parameter is an object that will be passed as payload to the Ajax request. `success` is the jQuery success callback, while `error` is the jQuery error callback.







---------------------

# Testing

## Unit Tests

See `./docs/developer/testing/unit-tests.md`


## Test Instance

See `./docs/developer/testing/test-instance.md`
