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
    'queue')
        exec bash -c "until [ -S /var/run/php5-fpm.sock ]; do echo Waiting php; sleep 1; done; exec php artisan dms:queuelisten -v"
    ;;
    *)
        echo "K-Box start.sh script: Command ${COMMAND} invalid, expected one of (php|apache|queue)"
   ;;
esac