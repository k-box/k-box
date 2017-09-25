[![build status](https://git.klink.asia/main/k-box/badges/master/build.svg)](https://git.klink.asia/main/k-box/commits/master) ![latest version](https://img.shields.io/badge/version-0.18.0-blue.svg)

# K-Box

The K-Box is a web application designed for handling document management inside an Organization or on a Project basis.

The K-Box is built on top of [Laravel](https://laravel.com/) and distributed via Docker image.

## Usage

_to be written_


---------------------

## DMS Command Line

The DMS offers various commands that can be executed in Command Line. 
See [`docs/developer/commands`](./docs/developer/commands/index.md) for the full list.


---------------------

## Asynchronous Jobs and Queue Runner

The DMS will use the Laravel Queue capability for handling long running jobs, like sending email, perform document import and others.

To start the asynchronous job lister execute

	php artisan dms:queuelisten

from the command line (inside the K-Box root folder).

This command will stay alive until is terminated (Ctrl+C or an unhandled exception occur).

---------------------

## Testing

The K-Box code is covered by unit tests.

For more information see [Executing Unit Tests](./docs/developer/testing/unit-tests.md) and [Using the test instance](./docs/developer/testing/test-instance.md)
