#!/bin/bash

KLINK_DMS_DIR=${KLINK_DMS_DIR:-/var/www/dms}
KLINK_DMS_APP_KEY=${KLINK_DMS_APP_KEY:-$(date| md5sum)}
KLINK_SETUP_WWWUSER=${KLINK_SETUP_WWWUSER:-www-data}
KLINK_DMS_ADMIN_USERNAME=${KLINK_DMS_ADMIN_USERNAME:-}
KLINK_DMS_ADMIN_PASSWORD=${KLINK_DMS_ADMIN_PASSWORD:-}
KLINK_PHP_POST_MAX_SIZE=${KLINK_PHP_POST_MAX_SIZE:-120M}
KLINK_PHP_UPLOAD_MAX_FILESIZE=${KLINK_PHP_UPLOAD_MAX_FILESIZE:-100M}
KLINK_DMS_APP_ENV=${KLINK_DMS_APP_ENV:-production}
KLINK_DMS_APP_DEBUG=${KLINK_DMS_APP_DEBUG:-false}
KLINK_DMS_MAX_UPLOAD_SIZE=${KLINK_DMS_MAX_UPLOAD_SIZE:-100000}
KLINK_PHP_MEMORY_LIMIT=${KLINK_PHP_MEMORY_LIMIT:-500M}

function startup_config () {
    echo "Configuring K-Box..."
    echo "- Writing php configuration..."

    # Configuring PHP Timezone
    sed -i "s|^;\?\(date.timezone =\).*$|\1 ${KLINK_PHP_TIMEZONE}|" /etc/php5/cli/php.ini &&
    sed -i "s|^;\?\(date.timezone =\).*$|\1 ${KLINK_PHP_TIMEZONE}|" /etc/php5/fpm/php.ini &&
    # Set post and upload size for php if customized for the specific deploy
    sed -i "s|^;\?\(post_max_size =\).*$|\1 ${KLINK_PHP_POST_MAX_SIZE}|" /etc/php5/fpm/php.ini &&
    sed -i "s|^;\?\(post_max_size =\).*$|\1 ${KLINK_PHP_POST_MAX_SIZE}|"  /etc/php5/cli/php.ini &&
    sed -i "s|^;\?\(upload_max_filesize =\).*$|\1 ${KLINK_PHP_UPLOAD_MAX_FILESIZE}|" /etc/php5/fpm/php.ini &&
    sed -i "s|^;\?\(upload_max_filesize =\).*$|\1 ${KLINK_PHP_UPLOAD_MAX_FILESIZE}|"  /etc/php5/cli/php.ini &&
    sed -i "s|^;\?\(memory_limit =\).*$|\1 ${KLINK_PHP_MEMORY_LIMIT}|" /etc/php5/fpm/php.ini &&

    write_config &&
    init_empty_dir $KLINK_DMS_DIR/storage && 
    chgrp -R $KLINK_SETUP_WWWUSER $KLINK_DMS_DIR &&
    chmod -R g+rw $KLINK_DMS_DIR/bootstrap/cache &&
    chmod -R g+rw $KLINK_DMS_DIR/storage &&
    wait_mariadb &&
    update_dms &&
	echo "K-Box is now configured, now starting php-fpm..."
}

function helper_replace_in_file_regexp() {
    echo "Replacing $1 with $2 in $3"
    sed -i "s|${1}|${2}|g" ${3}
    return $?
}

function write_config() {
    echo "- Writing env file..."

    KLINK_DMS_CORE_ADDRESS=${KLINK_DMS_CORE_ADDRESS:-https://$KCORE_1_PORT_443_TCP_ADDR/kcore}
    echo "Using $KLINK_DMS_CORE_ADDRESS as core address"
    
	cat > ${KLINK_DMS_DIR}/.env <<-EOM &&
		APP_ENV=${KLINK_DMS_APP_ENV}
		APP_DEBUG=${KLINK_DMS_APP_DEBUG}
		DMS_USE_HTTPS=true

		DMS_INSTITUTION_IDENTIFIER=${KLINK_CORE_ID}
		APP_KEY=${KLINK_DMS_APP_KEY:1:32}
		APP_URL=${KLINK_DMS_APP_URL}
		DMS_IDENTIFIER=${KLINK_DMS_IDENTIFIER}
		DMS_CORE_ADDRESS=${KLINK_DMS_CORE_ADDRESS}
		DMS_CORE_USERNAME=${KLINK_DMS_CORE_USERNAME}
		DMS_CORE_PASSWORD=${KLINK_DMS_CORE_PASSWORD}
		DMS_MAX_UPLOAD_SIZE=${KLINK_DMS_MAX_UPLOAD_SIZE}
		DB_NAME=${KLINK_DMS_DB_NAME}
		DB_HOST=${KLINK_DMS_DB_HOST}
		DB_USERNAME=${KLINK_DMS_DB_USERNAME}
		DB_PASSWORD=${KLINK_DMS_DB_PASSWORD}
		DB_TABLE_PREFIX=${KLINK_DMS_DB_TABLE_PREFIX}
	EOM
	echo "- ENV file written! $KLINK_DMS_DIR/.env"
}

function update_dms() {
    cd ${KLINK_DMS_DIR} &&
    echo "- Launching dms:update procedure..." &&
    su -s /bin/sh -c "php artisan dms:update --no-test -vv" $KLINK_SETUP_WWWUSER &&
    create_admin
}

function wait_mariadb () {
    wait_command mariadb_test 6 10
}

function mariadb_test () {
   php -f /usr/local/bin/db-connect-test.php -- -d $KLINK_DMS_DB_NAME -H ${KLINK_DMS_DB_HOST} -u ${KLINK_DMS_DB_USERNAME} -p ${KLINK_DMS_DB_PASSWORD}
}

function wait_command () {
    local command=$1
    local retry_times=$2
    local sleep_seconds=$3

    for i in $(seq $retry_times); do
        echo "- Waiting for ${command} ... Retry $i"
        $command && return 0 || sleep $sleep_seconds
    done
    return 1
}

function create_admin () {
    su -s /bin/sh -c "php artisan dms:create-admin $KLINK_DMS_ADMIN_USERNAME $KLINK_DMS_ADMIN_PASSWORD" $KLINK_SETUP_WWWUSER

    local ret=$?
    echo "Returned $ret"
    if [ $ret -eq 2 ]; then
        echo "Return 2 means the user is already there, good for us"
        return 0
    else
        echo "Returning $ret back to caller"
        return $ret
    fi
}

function init_empty_dir() {
    local dir_to_init=$1

    echo "- Checking storage directory structure..."

    if [ ! -d "${dir_to_init}/app/projects/avatars" ]; then
        mkdir -p "${dir_to_init}/app/projects/avatars"
        echo "-- [app/projects/avatars] created."
    fi
    if [ ! -d "${dir_to_init}/documents" ]; then
        mkdir -p "${dir_to_init}/documents"
        echo "-- [documents] created."
    fi
    if [ ! -d "${dir_to_init}/framework/cache" ]; then
        mkdir -p "${dir_to_init}/framework/cache"
        echo "-- [framework/cache] created."
    fi
    if [ ! -d "${dir_to_init}/framework/sessions" ]; then
        mkdir -p "${dir_to_init}/framework/sessions"
        echo "-- [framework/sessions] created."
    fi
    if [ ! -d "${dir_to_init}/framework/views" ]; then
        mkdir -p "${dir_to_init}/framework/views"
        echo "-- [framework/views] created."
    fi
    if [ ! -d "${dir_to_init}/logs" ]; then
        mkdir -p "${dir_to_init}/logs"
        echo "-- [logs] created."
    fi

}

startup_config >&2 &&
exec /usr/sbin/php5-fpm -F
