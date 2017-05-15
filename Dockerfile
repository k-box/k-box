FROM buildpack-deps:jessie-curl

RUN apt-get update \
    && apt-get install -y locales \
	&& apt-get clean \
	&& rm -r /var/lib/apt/lists/*

# Set the locale
RUN locale-gen "en_US.UTF-8" \
    && DEBIAN_FRONTEND=noninteractive dpkg-reconfigure locales \
	&& locale-gen "C.UTF-8" \
	&& DEBIAN_FRONTEND=noninteractive dpkg-reconfigure locales \
	&& /usr/sbin/update-locale LANG="C.UTF-8"
ENV LANG C.UTF-8
ENV LANGUAGE C.UTF-8:en
ENV LC_ALL C.UTF-8

RUN sed -i 's/\([[:blank:]]*main[[:blank:]]*\)/\1 non-free/' /etc/apt/sources.list \
    && apt-get update \
    && apt-get install -y \
        apache2 \
        libapache2-mod-fastcgi \
        php5-cli \
        php5-curl \
        php5-fpm \
        php5-gd \
        php5-intl \
        php5-mcrypt \
        php5-mysqlnd \
        xz-utils \
    && apt-get install --no-install-recommends -y -qq \
        graphicsmagick-imagemagick-compat \
    && apt-get clean \
    && rm -r /var/lib/apt/lists/*

COPY docker/configs/php5-fpm.conf /etc/apache2/conf-available/php5-fpm.conf
COPY docker/dms-kcore-virtualhost.conf /etc/apache2/sites-available/dms-kcore-virtualhost.conf
COPY docker/configs/mpm_event.conf /etc/apache2/mods-available/mpm_event.conf
COPY docker/certs/klink-root-ca.crt /usr/local/share/ca-certificates/klink-root-ca.crt

ENV KLINK_PHP_WWWHTML_DIR /var/www/html
ENV KLINK_PHP_MAX_EXECUTION_TIME 120
ENV KLINK_PHP_MAX_INPUT_TIME 120
ENV KLINK_PHP_MEMORY_LIMIT 288M
ENV KLINK_PHP_POST_MAX_SIZE 20M
ENV KLINK_PHP_UPLOAD_MAX_FILESIZE 60M

RUN php5enmod mcrypt \
    && chmod 644 /usr/local/share/ca-certificates/klink-root-ca.crt \
    && update-ca-certificates \
    && mkdir ${KLINK_PHP_WWWHTML_DIR} -p \
    && chown root:root ${KLINK_PHP_WWWHTML_DIR} -R \
    && a2dissite dms-kcore-virtualhost 000-default default-ssl \
    # && sed -i 's|^\(Listen 80\).*$|# \1|' /etc/apache2/ports.conf \
    && a2ensite dms-kcore-virtualhost \
    && a2enmod ssl rewrite actions fastcgi alias \
    && a2enconf php5-fpm \
    && sed -i "s|^;\?opcache.enable=.*|opcache.enable=1|" /etc/php5/fpm/php.ini \
    && sed -i "s|^;\?\(max_execution_time =\).*$|\1 ${KLINK_PHP_MAX_EXECUTION_TIME}|" /etc/php5/fpm/php.ini \
    && sed -i "s|^;\?\(max_execution_time =\).*$|\1 ${KLINK_PHP_MAX_EXECUTION_TIME}|"  /etc/php5/cli/php.ini \
    && sed -i "s|^;\?\(max_input_time =\).*$|\1 ${KLINK_PHP_MAX_INPUT_TIME}|" /etc/php5/fpm/php.ini \
    && sed -i "s|^;\?\(max_input_time =\).*$|\1 ${KLINK_PHP_MAX_INPUT_TIME}|"  /etc/php5/cli/php.ini \
    && sed -i "s|^;\?\(memory_limit =\).*$|\1 ${KLINK_PHP_MEMORY_LIMIT}|" /etc/php5/fpm/php.ini \
    && sed -i "s|^;\?\(post_max_size =\).*$|\1 ${KLINK_PHP_POST_MAX_SIZE}|" /etc/php5/fpm/php.ini \
    && sed -i "s|^;\?\(post_max_size =\).*$|\1 ${KLINK_PHP_POST_MAX_SIZE}|"  /etc/php5/cli/php.ini \
    && sed -i "s|^;\?\(upload_max_filesize =\).*$|\1 ${KLINK_PHP_UPLOAD_MAX_FILESIZE}|" /etc/php5/fpm/php.ini \
    && sed -i "s|^;\?\(upload_max_filesize =\).*$|\1 ${KLINK_PHP_UPLOAD_MAX_FILESIZE}|"  /etc/php5/cli/php.ini \
    && sed -i 's@^.*error_log.*$@error_log = /proc/self/fd/2@' /etc/php5/fpm/php-fpm.conf \
    && sed -i 's@^.*access.log*$@access.log = /proc/self/fd/2@' /etc/php5/fpm/pool.d/www.conf

COPY docker/apache2-foreground.sh /usr/local/bin/apache2-foreground.sh
COPY docker/apache2-startup-config.sh /usr/local/bin/apache2-startup-config.sh
COPY docker/php-start.sh /usr/local/bin/php-start.sh
COPY docker/start.sh /usr/local/bin/start.sh
COPY docker/db-connect-test.php /usr/local/bin/db-connect-test.php

RUN chmod +x /usr/local/bin/apache2-foreground.sh && \
    chmod +x /usr/local/bin/apache2-startup-config.sh && \
    chmod +x /usr/local/bin/php-start.sh && \
    chmod +x /usr/local/bin/start.sh

COPY deploy-screens/index.html /var/www/html/index.html

COPY . /var/www/dms/

ENV KBOX_STORAGE "/var/www/dms/storage"

WORKDIR /var/www/dms

# CMD envsubst < .env.example > .env && php artisan migrate --force && chgrp -R www-data /var/www/html/storage && chmod -R g+rw storage/ && exec php-fpm

EXPOSE 80

# This container can do apache, php-fpm or php artisan dms:queue according to the need.
# The entrypoint start command accept the following parameter values:
# - php: Start php-fpm and writes the environment configuration
# - apache: Start the Apache integrated webserver
# - queue: Start the Artisan queue listener for asynchronous jobs handling

ENTRYPOINT ["start.sh"]

