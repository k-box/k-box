

[![build status](https://git.klink.asia/klinkdms/dms/badges/master/build.svg)](https://git.klink.asia/klinkdms/dms/commits/master) ![latest version](https://img.shields.io/badge/version-0.11.0-blue.svg) ![dev version](https://img.shields.io/badge/dev-0.12.0-orange.svg)

# K-Link DMS

**version 0.11.0**

The K-Link DMS is built on top of [Laravel](https://laravel.com/) 5.1 (LTS).

This readme file is reserved for **developers**. For user oriented documentation please see the folder [`docs/user`](./docs/user/en) available in this repository.


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

The environment configuration uses a `.env` file in the the DMS project root folder. The `.env` file contains all the sensible configuration (local url, tokens, password, etc.).

The following table explains the configuration parameter that can (or must) be included in the `.env` file.

| parameter                             | required     | type    | default value | description |
|---------------------------------------|--------------| --------|---------------|-------------|
| `APP_ENV`                             | **required** | string  |               | the environment (when developing is highly encoraged to use local) |
| `APP_DEBUG`                           |              | boolean | false         | Set to true will enable debug, false will prevent debug information to show up |
| `APP_KEY`                             | **required** | string  |               | Encryption Key. This key is used by the Illuminate encrypter service and should be set to a random, 32 character string, otherwise the encrypted strings will not be safe. **Please do this before deploying an application!** |
| `APP_URL`                             | **required** | string  |               | The url of the public folder, if you use a virtual host insert the virtual host url here |
| `DB_USERNAME`                         | **required** | string  |               | The database user that has only priviledge over the database specified by `DB_NAME` |
| `DB_PASSWORD`                         | **required** | string  |               | The database user password |
| `DB_HOST`                             |              | string  | localhost     | The database sever host |
| `DB_NAME`                             | **required** | string  |               | The Database name |
| `DB_TABLE_PREFIX`                     | **required** | string  |               | The table prefix for each database table |
| `SESSION_DRIVER`                      |              | string  | file          | How the authentication session is maintaned. Supported: "file", "cookie", "database", "apc", "memcached", "redis", "array" |
| `DMS_INSTITUTION_IDENTIFIER`          | **required** | string  |               | The institution identifier that is required for communicating with the K-Link Core |
| `DMS_CORE_ADDRESS`                    | **required** | url     |               | The URL of the K-Link Core that will be used by the DMS. |
| `DMS_CORE_USERNAME`                   | **required** | string  |               | The username for authenticating on the core. |
| `DMS_CORE_PASSWORD`                   | **required** | string  |               | The password for authenticating on the core. |
| `DMS_IDENTIFIER`                      |              | string  |               | The unique identifier for the DMS instance |
| `DMS_ARE_GUEST_PUBLIC_SEARCH_ENABLED` |              | boolean |               | Tell if the DMS will allow guest user to perform public search over K-Link instance |
| `DMS_USE_HTTPS`                       |              | boolean | false         | Force the use of HTTPS |

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
DB_TABLE_PREFIX=kdms
```

**Important: make sure that all the required parameters are inserted into the `.env` file.**

**It's important that the parameter `DMS_INSTITUTION_IDENTIFIER`, `DMS_CORE_ADDRESS`, `DMS_CORE_USERNAME`, `DMS_CORE_PASSWORD`, `DMS_IDENTIFIER` are configured in the `.env` file before proceeding** 
(contact your K-Link Development team referent to get the values for those parameters).

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

## Setting up the production environment

### Environment parameters

The following table explain the configuration parameter that can (or must) be included in the `.env` file in the DMS project root folder.

| parameter         |          | type    | recommended value | description |
|-------------------|----------| --------|-------------------|-------------|
| `APP_ENV`         | required | string  | production   | the environment (when developing is highly encoraged to use local) |
| `APP_KEY`         | required | string  |         | Encryption Key. This key is used by the Illuminate encrypter service and should be set to a random, 32 character string, otherwise these encrypted strings will not be safe. Please do this before deploying an application! |
| `APP_URL`         | required | string  |         | The url of the public folder, if you use a virtual host insert the virtual host url here |
| `DB_USERNAME`     | required | string  |         | The database user that has only priviledge over the database specified by `DB_NAME` |
| `DB_PASSWORD`     | required | string  |         | The database user password |
| `DB_HOST`         |          | string  |         | The database sever host |
| `DB_NAME`         | required | string  |         | The Database name |
| `DB_TABLE_PREFIX` | required | string  |     | The table prefix for each database table |
| `SESSION_DRIVER`  |     | string  |         | How the authentication session is maintaned. Supported: "file", "cookie", "database", "apc", "memcached", "redis", "array" |
| `DMS_INSTITUTION_IDENTIFIER` | string |  | The institution identifier that is required for communicating with the K-Link Core |
| `DMS_CORE_ADDRESS`  | required | url  |         | The URL of the K-Link Core that will be used by the DMS. |
| `DMS_CORE_USERNAME` | required | string  |         | The username for authenticating on the core. |
| `DMS_CORE_PASSWORD` | required | string  |         | The password for authenticating on the core. |
| `DMS_IDENTIFIER`    | required | string  |         | The unique identifier for the DMS instance |
| `DMS_ARE_GUEST_PUBLIC_SEARCH_ENABLED`    |  | boolean  |         | Tell if the DMS will allow guest user to perform public search over K-Link instance (default true) |

The following block is an example of a `.env` file.

```
APP_ENV=local 
APP_DEBUG=false
APP_KEY=RANDOM_STRING
APP_URL=http://localhost/dms/
DB_USERNAME=dms
DB_PASSWORD=&middot;&middot;&middot;&middot;
DB_HOST=localhost
DB_NAME=dms
DB_TABLE_PREFIX=kdms
```

If an alias is used remember to update the `RewriteBase` rule in `.htaccess` file situated in the public folder.



# Testing

## Unit Tests

See `./docs/developer/testing/unit-tests.md`


## Test Instance

See `./docs/developer/testing/test-instance.md`

# Frontend localization

The javascript localization is handled with the help of the RequireJS [i18n plugin](https://github.com/requirejs/i18n).

Language files are constructed at build time and copied in `/public/js/nls/` folder. Please do not edit that files.

The system will load `/public/js/nls/lang.js` which contains the english fallback and then will try to load the specific locales if available (and specified in the `require-config.blade.php` file)

Construction of the language files is performed on the basis of the Laravel language files and a custom made command that extracts specific strings from them in order to be inserted in the javascript language file. Configuration options resides in the `config/localization.php` file

to generate the javascript language files launch

```bash
php artisan dms:lang-publish 
```

Localization files will be available through the `language` module (RequireJS module). The loaded language file will be the one for the specific locale of the page, i.e. if the page `lang` is `en` only the `en` strings will be available. 

This code block shows how to reference the `language` module

```js
define(['language'], function(Lang) {
    
	// Use Lang.trans( ...) 

});
```

## `language` module API

### `trans`

```
trans ( messageKey : string, replacements : ReplacementObject ) : string
```

_Parameters:_

- `messageKey`: the key of the translation string to retrieve
- `replacements`: if the translation string contains placeholder, like `:attribute`, you could substitute them with something more useful, see [ReplacementObject](#replacementobject) 

_Returns:_

the localized string, with applied replacements or messageKey if the corresponding localized string cannot be found


### `choice`

```
choice ( messageKey : string, count : number, replacements : ReplacementObject ) : boolean
```
_Parameters:_

- `messageKey`: the key of the translation string to retrieve
- `count`: the number of elements used for the selection of the proper translation string
- `replacements`: if the translation string contains placeholder, like `:attribute`, you could substitute them with something more useful, see [ReplacementObject](#replacementobject)

_Returns:_

the localized string, with applied replacements or messageKey if the corresponding localized string cannot be found

**Important Notice**: currently only english pluralization rules are supported.

### `alternate`

```
trans ( messageKey : string, alternateMessageKey : string, basedOn : string, replacements : ReplacementObject ) : string
```

Choose the translation string from messageKey or alternateMessageKey based on the existence of basedOn key in replacements.

_Parameters:_

- `messageKey`: the key of the translation string to retrieve
- `alternateMessageKey`: the key of the translation string to retrieve as a fallback
- `basedOn`: the property name to check inside replacements to decide if use `messageKey` or `alternateMessageKey`
- `replacements`: if the translation string contains placeholder, like `:attribute`, you could substitute them with something more useful, see [ReplacementObject](#replacementobject) 

_Returns:_

the localized string, with applied replacements, using messageKey if the property basedOn exists and not null in replacements, using alternateMessageKey otherwise


### `has`

```
has ( messageKey : string ) : boolean
```

Tests if the given key is defined in the translation set.

_Parameters:_

- `messageKey`: the key of the translation string to retrieve

_Returns:_

`true` if the localized string exists, `false` otherwise.


### ReplacementObject 

The replacement object is a plain Javascript object in which keys must be named parameters contained in the translation string and values could be any string. Values will be substituted to placeholders in the translation string.

For example, let's consider the following translation string

```
":page cannot be found"
```

the replacement object that can be used is

```json
{ "page": "value" }
```

## Date and time localization

The trait `KlinkDMS\Traits\LocalizableDateFields` has been added to offer localized date and time output for Eloquent Models.

If you want to offer date and time localization use `Jenssegers\Date\Date` as the datetime class instead of Carbon.

You can convert a `Carbon` instance to `Jenssegers\Date\Date` by using `Jenssegers\Date\Date::instance( /* Carbon */ $carbon_date)`.

the class `Jenssegers\Date\Date` is an extension of `Carbon`.

For chaining there is also

- `localized_date(DateTime $dt)` that takes a php `DateTime` instance (also a `Carbon` instance) and converts it to a localizable Date instance

Other two commond shortcut methods are available

- `localized_date_human_diff(DateTime $dt)` that ouputs a human diff between the current date and the specified date if the difference in days is less than 2
- `localized_date_full(DateTime $dt)` outputs a localized long date/time (according to the translation `units.date_format_full`)
- `localized_date_short(DateTime $dt)` outputs a localized short date (according to the translation `units.date_format`)


