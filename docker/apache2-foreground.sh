#!/bin/bash

echo "Starting K-Box Apache webserver..."

# Remove undesirable side effects of CDPATH variable
unset CDPATH
# Change current working directory to the directory contains this script
cd "$( dirname "${BASH_SOURCE[0]}" )"

. /etc/default/apache2
. /etc/apache2/envvars

# This solves some issues when support directories are missing
mkdir -p "$APACHE_LOCK_DIR" && chgrp "$APACHE_RUN_GROUP" "$APACHE_LOCK_DIR" && chmod g+w "$APACHE_LOCK_DIR"
mkdir -p "$APACHE_RUN_DIR" && chgrp "$APACHE_RUN_GROUP" "$APACHE_RUN_DIR" && chmod g+w "$APACHE_RUN_DIR"

exec /usr/sbin/apache2 -D FOREGROUND
