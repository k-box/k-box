#!/bin/bash

## The public URL on which the K-Box will be available
KBOX_APP_URL=${KBOX_APP_URL:-${KLINK_DMS_APP_URL:-}}
## Application key
KBOX_APP_KEY=${KBOX_APP_KEY:-${KLINK_DMS_APP_KEY:-}}
## Application environment name
KBOX_APP_ENV=${KBOX_APP_ENV:-${KLINK_DMS_APP_ENV:-production}}
## Enable/Disable the debug mode
KBOX_APP_DEBUG=${KBOX_APP_DEBUG:-${KLINK_DMS_APP_DEBUG:-false}}

## KLINK_DMS_ADMIN_USERNAME
KBOX_ADMIN_USERNAME=${KBOX_ADMIN_USERNAME:-${KLINK_DMS_ADMIN_USERNAME:-}}
KBOX_ADMIN_PASSWORD=${KBOX_ADMIN_PASSWORD:-${KLINK_DMS_ADMIN_PASSWORD:-}}
##
KLINK_PHP_POST_MAX_SIZE=${KLINK_PHP_POST_MAX_SIZE:-120M}
##
KLINK_PHP_UPLOAD_MAX_FILESIZE=${KLINK_PHP_UPLOAD_MAX_FILESIZE:-100M}
##
KLINK_DMS_MAX_UPLOAD_SIZE=${KLINK_DMS_MAX_UPLOAD_SIZE:-100000}

##
KLINK_PHP_MEMORY_LIMIT=${KLINK_PHP_MEMORY_LIMIT:-500M}
##
DMS_USE_HTTPS=${DMS_USE_HTTPS:-false}
## User under which the data will run
KBOX_SETUP_USER=www-data
## Directory where the code is located
KBOX_DIR=/var/www/dms

function startup_config () {
    echo "Configuring K-Box..."
    echo "- Writing php configuration..."

    # Configuring PHP Timezone
    sed -i "s|^;\?\(date.timezone =\).*$|\1 ${KLINK_PHP_TIMEZONE}|" /usr/local/etc/php/conf.d/php-timezone.ini
    
    # Set post and upload size for php if customized for the specific deploy
    cat > /usr/local/etc/php/conf.d/php-runtime.ini <<-EOM &&
		post_max_size=${KLINK_PHP_POST_MAX_SIZE}
        upload_max_filesize=${KLINK_PHP_UPLOAD_MAX_FILESIZE}
        memory_limit=${KLINK_PHP_MEMORY_LIMIT}
        max_input_time=${KLINK_PHP_MAX_INPUT_TIME}
        max_execution_time=${KLINK_PHP_MAX_EXECUTION_TIME}
	EOM

    write_config &&
    init_empty_dir $KBOX_DIR/storage && 
    echo "Changing folder groups and permissions" &&
    chgrp -R $KBOX_SETUP_USER $KBOX_DIR/storage &&
    chgrp -R $KBOX_SETUP_USER $KBOX_DIR/bootstrap/cache &&
    chgrp -R $KBOX_SETUP_USER $KBOX_DIR/vendor/avvertix/laravel-tus-upload/bin/ &&
    chmod +x $KBOX_DIR/vendor/avvertix/laravel-tus-upload/bin/tusd-linux &&
    chmod -R g+rw $KBOX_DIR/bootstrap/cache &&
    chmod -R g+rw $KBOX_DIR/storage &&
    normalize_line_endings &&
    wait_mariadb &&
    update_dms &&
    chgrp -R $KBOX_SETUP_USER $KBOX_DIR/storage/logs &&
    chgrp -R $KBOX_SETUP_USER $KBOX_DIR/bootstrap/cache &&
    chmod -R g+rw $KBOX_DIR/bootstrap/cache &&
    chmod -R g+rw $KBOX_DIR/storage/logs &&
	echo "K-Box is now configured."
}

function write_config() {

    if [ -z "$KBOX_APP_URL" ]; then
        # application URL not set
        echo "**************"
        echo "K-Box public URL not set. Set the public URL using KBOX_APP_URL."
        echo "**************"
        return 1001
    fi

    echo "- Writing env file..."

    KLINK_DMS_CORE_ADDRESS=${KLINK_DMS_CORE_ADDRESS:-https://$KCORE_1_PORT_443_TCP_ADDR/kcore}
    
	cat > ${KBOX_DIR}/.env <<-EOM &&
		APP_KEY=${KBOX_APP_KEY}
		APP_URL=${KBOX_APP_URL}
		APP_ENV=${KBOX_APP_ENV}
		APP_DEBUG=${KBOX_APP_DEBUG}
		DMS_USE_HTTPS=${DMS_USE_HTTPS}
		DMS_INSTITUTION_IDENTIFIER=${KLINK_CORE_ID}
		APP_INTERNAL_URL=${KLINK_DMS_APP_INTERNAL_URL}
		DMS_IDENTIFIER=${KLINK_DMS_IDENTIFIER}
		DMS_CORE_ADDRESS=${KLINK_DMS_CORE_ADDRESS}
		DMS_MAX_UPLOAD_SIZE=${KLINK_DMS_MAX_UPLOAD_SIZE}
		DB_NAME=${KLINK_DMS_DB_NAME}
		DB_HOST=${KLINK_DMS_DB_HOST}
		DB_USERNAME=${KLINK_DMS_DB_USERNAME}
		DB_PASSWORD=${KLINK_DMS_DB_PASSWORD}
		DB_TABLE_PREFIX=${KLINK_DMS_DB_TABLE_PREFIX}
        TUSUPLOAD_USE_PROXY=true
        TUSUPLOAD_HOST=0.0.0.0
        TUSUPLOAD_HTTP_PATH=/tus-uploads/
        TUSUPLOAD_URL=${KBOX_APP_URL}tus-uploads/
	EOM

    # generate APP_KEY if not already set
    php artisan config:clear
    php artisan kbox:key

	echo "- ENV file written! $KBOX_DIR/.env"
}

function update_dms() {
    cd ${KBOX_DIR}
    echo "- Launching dms:update procedure..."
    php artisan dms:update --no-test -vv
    create_admin
}

function wait_mariadb () {
    wait_command mariadb_test 6 10
}

function mariadb_test () {
   php -f /usr/local/bin/db-connect-test.php -- -d "${KLINK_DMS_DB_NAME}" -H "${KLINK_DMS_DB_HOST}" -u "${KLINK_DMS_DB_USERNAME}" -p "${KLINK_DMS_DB_PASSWORD}"
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

function normalize_line_endings() {

    cp $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/pre-create $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/pre-create-original \
    && cp $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-receive $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-receive-original \
    && cp $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-finish $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-finish-original \
    && cp $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-terminate $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-terminate-original \

    tr -d '\r' < $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/pre-create-original > $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/pre-create
    tr -d '\r' < $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-receive-original > $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-receive
    tr -d '\r' < $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-finish-original > $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-finish
    tr -d '\r' < $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-terminate-original > $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-terminate

    chgrp -R $KBOX_SETUP_USER $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/ \
    && chmod -R +x $KBOX_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/
}

function create_admin () {

    if [ -z "$KBOX_ADMIN_USERNAME" ] &&  [ -z "$KBOX_ADMIN_PASSWORD" ]; then
        # if both username and password are not defined or empty, tell to create the user afterwards an end return
        echo "**************"
        echo "Remember to create an admin user: php artisan create-admin --help"
        echo "**************"
        return 0
    fi

    if [ -z "$KBOX_ADMIN_USERNAME" ] &&  [ ! -z "$KBOX_ADMIN_PASSWORD" ]; then
        # username not set, but password set => error
        echo "**************"
        echo "Admin email not specified. Please specify an email address using the variable KBOX_ADMIN_USERNAME"
        echo "**************"
        return 1000
    fi
    
    if [ ! -z "$KBOX_ADMIN_USERNAME" ] &&  [ -z "$KBOX_ADMIN_PASSWORD" ]; then
        # username set, but empty password => the user needs to be created after the setup
        echo "**************"
        echo "Skipping creation of default administrator. Use php artisan create-admin after the startup is complete."
        echo "**************"
        return 0
    fi

    su -s /bin/sh -c "php artisan create-admin '$KBOX_ADMIN_USERNAME' --password '$KBOX_ADMIN_PASSWORD'" $KBOX_SETUP_USER

    local ret=$?
    if [ $ret -eq 2 ]; then
        echo "Admin user is already there, good for us"
        return 0
    elif [ $ret -eq 0 ]; then
        return 0
    else
        echo "Admin user creation fail. Error $ret"
        return $ret
    fi
}

## Initialize an empty storage directory with the required default folders
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

startup_config >&2
