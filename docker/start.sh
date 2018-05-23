#!/bin/bash

COMMAND=${1}

echo "K-Box start in progress..."

case "${COMMAND}" in
    *)
        /usr/local/bin/configure.sh && exec /usr/bin/supervisord
   ;;
esac