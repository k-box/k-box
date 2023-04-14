<p align="center"><img src="./public/k-box-logo.png" width="400"></p>

<p align="center">
<a href="https://github.com/k-box/k-box/actions?query=workflow%3ACI+branch%3Amaster+" target="_blank">
    <img src="https://github.com/k-box/k-box/workflows/CI/badge.svg" alt="Build Status">
</a>
<a href="https://github.com/k-box/k-box/releases/latest" target="_blank">
    <img alt="latest release" src="https://img.shields.io/github/v/release/k-box/k-box">
</a>
<img alt="License AGPL-3.0" src="https://img.shields.io/github/license/k-box/k-box">
</p>

# K-Box

The digital tool for projects in the field: Web-based application to manage documents, images, videos and geodata. It contains a full content search, a translated interface into several languages and it connects easily to the K-Link services.

**[Visit the K-Link technology website](https://oneofftech.xyz/k-link/)** for more information!

If you find any issues with this application, please report them at the [issue tracker](./issues). Contributions are both encouraged and appreciated. If you would like to contribute, please check the website for more information.

The upstream repository is at: https://github.com/k-box/k-box

<p align="center"><img src="./public/k-box-screenshot.png" width="960"></p>

## Installation

K-Box can be installed on most operating systems. The setup is heavily based on [Docker](https://www.docker.com/).

### Prerequisites

- Check the [system requirements](./docs/installation/requirements.md).
- Use an operating system [supported by Docker](https://docs.docker.com/install/#server) (ideally GNU/Linux; we use [Debian](https://debian.org))
- Make sure you have installed the latest version of [Docker](https://docs.docker.com/install/linux/docker-ce/debian/) and [Docker Compose](https://docs.docker.com/compose/install/).

### Simplest installation

These few commands allow you to quickly install a K-Box **locally** on your computer for testing purposes.

* Create a directory: `mkdir k-box && cd k-box`
* Download configuration file: `curl -o docker-compose.yml https://raw.githubusercontent.com/k-box/k-box/master/docker-compose.example.yml`
* Start up services: `docker-compose up -d` or `sudo docker-compose up -d`, depending on your configuration of Docker (when running this for the first time, it will download a lot of data and take a while)
* Create the administrator: `docker-compose exec kbox php artisan create-admin admin@kbox.local` or `sudo docker-compose exec kbox php artisan create-admin admin@kbox.local`, depending on your configuration of Docker
* Visit your K-Box: [http://localhost:8080](http://localhost:8080/) (you can login to the K-Box with the username `admin@kbox.local` and the chosen password).

For installation on a server in the Internet or more configuration options, see the documentation on [installation of the K-Box](./docs/installation/installation.md).

## Components

The K-Box consists of different components:

| Name | Image | Based on | Description |
|------|-------|----------|-------------|
| [K-Box](./docs/index.md) application | `kbox` | PHP and Laravel | The interface of the knowledge management system |
| [K-Search API](https://github.com/k-box/k-search) | `ksearch` | PHP and Symfony 4 | Full text search component used for K-Link and K-Box |
| [K-Search Engine](https://github.com/k-box/k-search-engine) | `engine` | Apache SOLR | Open Source search engine pre-configured for the K-Search |
| Database | `database` | MariaDB | A database for the use of the K-Box web application. |

## Development

Programmers may check out the [developers documentation](./docs/developer/index.md)

## Testing

The K-Box code is covered by unit tests. For more information see [Executing Unit Tests](./docs/developer/testing.md).

## Contributing

**Your contribution is very welcome**. Find more information in our [contribution guide](./contributing.md).

## License

The K-Box is licensed under the [GNU Affero General Public License v3.0](./LICENSE.txt).

This program is Free Software: You can use, study, share and improve it at your will. Specifically you can redistribute and/or modify it under the terms of the [AGPL 3.0 license](./LICENSE.txt).
