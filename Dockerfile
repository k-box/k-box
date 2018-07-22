## Grabbing required binaries for the video processing part
FROM docker.klink.asia/images/video-processing-cli:0.3.1 AS videocli
# .. we just need this image so we can copy from it

FROM docker.klink.asia/main/docker-php:7.1 AS builder
## Installing the dependencies to be used in a later step.
# Will generate three directories:
# * /var/www/dms/bin/
# * /var/www/dms/vendor/
# * /var/www/dms/public/
WORKDIR /app
COPY . /app
RUN \
    composer install --no-dev --prefer-dist &&\
    composer run install-content-cli &&\
    composer run install-language-cli &&\
    composer run install-streaming-client
RUN \
    yarn config set cache-folder .yarn && \
    yarn install && \
    yarn run production

## Generating the real K-Box image
FROM php:7.1-fpm AS php

## Default environment variables
ENV KBOX_PHP_MAX_EXECUTION_TIME 120
ENV KBOX_PHP_MAX_INPUT_TIME 120
ENV KBOX_PHP_MEMORY_LIMIT 500M
ENV KBOX_DIR /var/www/dms

## Install libraries, envsubst, supervisor and php modules
RUN apt-get update -yqq && \
    apt-get install -yqq \ 
        locales \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        libbz2-dev \
        gettext \
        supervisor \
        cron \
        libmagickwand-dev \
    && docker-php-ext-install -j$(nproc) iconv mcrypt \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install bz2 zip exif pdo_mysql \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && apt-get clean \
    && rm -r /var/lib/apt/lists/*

## Forces the locale to UTF-8, suggestion from Marco Zanoni
RUN locale-gen "en_US.UTF-8" \
    && DEBIAN_FRONTEND=noninteractive dpkg-reconfigure locales \
 	&& locale-gen "C.UTF-8" \
 	&& DEBIAN_FRONTEND=noninteractive dpkg-reconfigure locales \
 	&& /usr/sbin/update-locale LANG="C.UTF-8"

## NGINX installation
### The installation procedure is heavily inspired from https://github.com/nginxinc/docker-nginx
RUN set -e; \
	NGINX_GPGKEY=573BFD6B3D8FBC641079A6ABABF5BD827BD9BF62; \
	NGINX_VERSION=1.14.0-1~stretch; \
	found=''; \
	apt-get update; \
	apt-get install -y gnupg; \
	for server in \
		ha.pool.sks-keyservers.net \
		hkp://keyserver.ubuntu.com:80 \
		hkp://p80.pool.sks-keyservers.net:80 \
		pgp.mit.edu \
	; do \
		echo "Fetching GPG key $NGINX_GPGKEY from $server"; \
		apt-key adv --keyserver "$server" --keyserver-options timeout=10 --recv-keys "$NGINX_GPGKEY" && found=yes && break; \
	done; \
	test -z "$found" && echo >&2 "error: failed to fetch GPG key $NGINX_GPGKEY" && exit 1; \
    echo "deb http://nginx.org/packages/debian/ stretch nginx" >> /etc/apt/sources.list \
	&& apt-get update \
	&& apt-get install --no-install-recommends --no-install-suggests -y \
						ca-certificates \
						nginx=${NGINX_VERSION} \
						# nginx-module-njs=${NJS_VERSION} \
						# gettext-base \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

## Configure cron to run Laravel scheduler
RUN echo '* * * * * php /var/www/dms/artisan schedule:run >> /dev/null 2>&1' | crontab -

## Copy NGINX default configuration
COPY docker/nginx-default.conf /etc/nginx/conf.d/default.conf

## Copy additional PHP configuration files
COPY docker/php/php-*.ini /usr/local/etc/php/conf.d/

## Override the php-fpm additional configuration added by the base php-fpm image
COPY docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/

## Copy supervisor configuration
COPY docker/supervisor/kbox.conf /etc/supervisor/conf.d/

## Copying custom startup scripts
COPY docker/configure.sh /usr/local/bin/configure.sh
COPY docker/start.sh /usr/local/bin/start.sh
COPY docker/db-connect-test.php /usr/local/bin/db-connect-test.php

RUN chmod +x /usr/local/bin/configure.sh && \
    chmod +x /usr/local/bin/start.sh


COPY deploy-screens/index.html /var/www/html/index.html

## Copy the application code
COPY \
    --chown=www-data:www-data \
    . /var/www/dms/

## Copy in the dependencies from the previous buildstep
COPY \
    --from=builder \
    --chown=www-data:www-data \
    /app/vendor/ \
    /var/www/dms/vendor/

COPY \
    --from=builder \
    --chown=www-data:www-data \
    /app/bin/ \
    /var/www/dms/bin/

COPY \
    --from=builder \
    --chown=www-data:www-data \
    /app/public/ \
    /var/www/dms/public/

COPY \
    --from=videocli \
    --chown=www-data:www-data \
    /video-processing-cli/ "/var/www/dms/bin/"

## Add frontend assets
# COPY \
#     --from=frontend \
#     --chown=www-data:www-data \
#     /app/public /var/www/dms/public

ENV KBOX_STORAGE "/var/www/dms/storage"

WORKDIR /var/www/dms

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/start.sh"]

