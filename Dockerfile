FROM php:7.0.21-fpm

## Environment variables to define the version of the packages to pull
ENV TINI_VERSION 0.15.0
### NGINX version, mainline for debian:jessie as the base image is based on debian:jessie
ENV NGINX_VERSION 1.11.9-1~jessie 

## Default environment variables
ENV KLINK_PHP_WWWHTML_DIR /var/www/html
ENV KLINK_PHP_MAX_EXECUTION_TIME 120
ENV KLINK_PHP_MAX_INPUT_TIME 120
ENV KLINK_PHP_MEMORY_LIMIT 288M
ENV KLINK_PHP_POST_MAX_SIZE 20M
ENV KLINK_PHP_UPLOAD_MAX_FILESIZE 60M

## Install libraries, envsubst, tini, supervisor and php modules
RUN apt-get update -yqq && \
    apt-get install -yqq \ 
        locales \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng12-dev \
        libbz2-dev \
        gettext \
        supervisor \
    && docker-php-ext-install -j$(nproc) iconv mcrypt \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install bz2 zip exif pdo_mysql \
    && curl -o /usr/local/bin/tini https://github.com/krallin/tini/releases/download/v${TINI_VERSION}/tini \
    && chmod +x /usr/local/bin/tini \
    && apt-get clean \
    && rm -r /var/lib/apt/lists/*

## Forces the locale to UTF-8, suggestion from Marco Zanoni
RUN locale-gen "en_US.UTF-8" \
    && DEBIAN_FRONTEND=noninteractive dpkg-reconfigure locales \
 	&& locale-gen "C.UTF-8" \
 	&& DEBIAN_FRONTEND=noninteractive dpkg-reconfigure locales \
 	&& /usr/sbin/update-locale LANG="C.UTF-8"

## NGINX installation
### This will install nginx for debian:jessie as the base PHP image is still based on debian:jessie
RUN apt-key adv --keyserver hkp://pgp.mit.edu:80 --recv-keys 573BFD6B3D8FBC641079A6ABABF5BD827BD9BF62 \
	&& echo "deb http://nginx.org/packages/mainline/debian/ jessie nginx" >> /etc/apt/sources.list \
	&& apt-get update \
	&& apt-get install --no-install-recommends --no-install-suggests -y \
						ca-certificates \
						nginx=${NGINX_VERSION} \
						# nginx-module-njs=${NJS_VERSION} \
						# gettext-base \
	&& rm -rf /var/lib/apt/lists/*

## Copy NGINX default configuration
COPY docker/nginx-default.conf /etc/nginx/conf.d/default.conf


## Copy additional PHP configuration files
COPY docker/php/php-*.ini /usr/local/etc/php/conf.d/

## Override the php-fpm additional configuration added by the base php-fpm image
COPY docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/

## Copy supervisor configuration
COPY docker/supervisor/kbox.conf /etc/supervisor/conf.d/

## Copying custom startup scripts
COPY docker/apache2-foreground.sh /usr/local/bin/apache2-foreground.sh
COPY docker/configure.sh /usr/local/bin/configure.sh
COPY docker/php-start.sh /usr/local/bin/php-start.sh
COPY docker/start.sh /usr/local/bin/start.sh
COPY docker/db-connect-test.php /usr/local/bin/db-connect-test.php

RUN chmod +x /usr/local/bin/apache2-foreground.sh && \
    chmod +x /usr/local/bin/configure.sh && \
    chmod +x /usr/local/bin/php-start.sh && \
    chmod +x /usr/local/bin/start.sh

COPY deploy-screens/index.html /var/www/html/index.html

## Copy the application code
COPY . /var/www/dms/

ENV KBOX_STORAGE "/var/www/dms/storage"

WORKDIR /var/www/dms

EXPOSE 80

# This container can do apache, php-fpm or php artisan dms:queue according to the need.
# The entrypoint start command accept the following parameter values:
# - php: Start php-fpm and writes the environment configuration
# - apache|nginx: Start the Nginx integrated webserver
# - queue: Start the Artisan queue listener for asynchronous jobs handling
# - tusd: Start the Artisan tus:start command for resumable upload support

ENTRYPOINT ["/usr/local/bin/start.sh"]
# ENTRYPOINT ["/usr/local/bin/tini", "--", "/usr/local/bin/start.sh"]

