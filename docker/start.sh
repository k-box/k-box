#!/bin/bash

DIR=$(pwd)
COMMAND=${1}

echo "K-Box start in progress..."

case "${COMMAND}" in
    'php')
        exec /usr/local/bin/php-start.sh
    ;;
    'apache')
        exec /usr/local/bin/apache2-foreground.sh
    ;;
    'all')
        /usr/local/bin/configure.sh && exec /usr/bin/supervisord
    ;;
    'queue')
        exec bash -c "until [ -S /var/run/php-fpm.sock ]; do echo Waiting php; sleep 1; done; exec su -c 'php artisan dms:queuelisten -v 2>&1' -s /bin/sh www-data"
    ;;
    *)
        /usr/local/bin/configure.sh && exec /usr/bin/supervisord
   ;;
esac