[![build status](https://git.klink.asia/main/k-box/badges/master/build.svg)](https://git.klink.asia/main/k-box/commits/master) ![latest version](https://img.shields.io/badge/version-0.18.0-blue.svg)


> **You are on a branch that overhauls the Institution handling and the supported K-Search**
>
> - [This branch will deprecate and partially remove support for Institutions](https://git.klink.asia/coordination/development/issues/348)
>
> - [This branch will target the whole search functions to the K-Search v3](https://git.klink.asia/coordination/development/issues/284)
> 
> This means that this branch cannot talk to K-Search (or K-Core) with API v2.x and require an empty search index to work. Upgrading a production K-Box to this branch is highly discouraged.


# K-Box

The K-Box is a web application designed for handling document management inside an Organization or on a Project basis.

The K-Box is built on top of [Laravel](https://laravel.com/) and distributed via Docker image.

## Getting started

_to be written_

## Development

Developer oriented material is stored in [`docs/developer/`](./docs/developer/)

## API

### Command Line

The K-Box offer various commands that can be executed in Command Line. 
See [`docs/developer/commands`](./docs/developer/commands/index.md) for the full list.


## Testing

The K-Box code is covered by unit tests.

For more information see [Executing Unit Tests](./docs/developer/testing/unit-tests.md) and [Using the test instance](./docs/developer/testing/test-instance.md)

## Contributing

Thank you for considering contributing to the K-Box! The [contribution guide](./contributing.md) is currently under heavy rewrite, but in the meantime you can still submit Pull Requests.

When submitting pull/merge requests always consider the `master` branch as your target.

## License

This project is licensed under the AGPL v3 license, see [LICENSE.txt](./LICENSE.txt).

