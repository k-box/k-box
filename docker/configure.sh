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
DMS_USE_HTTPS=${DMS_USE_HTTPS:-false}

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
    init_empty_dir $KLINK_DMS_DIR/storage && 
    echo "Changing folder groups and permissions" &&
    chgrp -R $KLINK_SETUP_WWWUSER $KLINK_DMS_DIR/storage &&
    chgrp -R $KLINK_SETUP_WWWUSER $KLINK_DMS_DIR/bootstrap/cache &&
    chgrp -R $KLINK_SETUP_WWWUSER $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/bin/ &&
    chmod +x $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/bin/tusd-linux &&
    chmod -R g+rw $KLINK_DMS_DIR/bootstrap/cache &&
    chmod -R g+rw $KLINK_DMS_DIR/storage &&
    normalize_line_endings &&
    wait_mariadb &&
    update_dms &&
    chgrp -R $KLINK_SETUP_WWWUSER $KLINK_DMS_DIR/storage/logs &&
    chgrp -R $KLINK_SETUP_WWWUSER $KLINK_DMS_DIR/bootstrap/cache &&
    chmod -R g+rw $KLINK_DMS_DIR/bootstrap/cache &&
    chmod -R g+rw $KLINK_DMS_DIR/storage/logs &&
	echo "K-Box is now configured."
}

function write_config() {
    echo "- Writing env file..."

    KLINK_DMS_CORE_ADDRESS=${KLINK_DMS_CORE_ADDRESS:-https://$KCORE_1_PORT_443_TCP_ADDR/kcore}
    echo "Using $KLINK_DMS_CORE_ADDRESS as core address"
    
	cat > ${KLINK_DMS_DIR}/.env <<-EOM &&
		APP_ENV=${KLINK_DMS_APP_ENV}
		APP_DEBUG=${KLINK_DMS_APP_DEBUG}
		DMS_USE_HTTPS=${DMS_USE_HTTPS}

		DMS_INSTITUTION_IDENTIFIER=${KLINK_CORE_ID}
		APP_KEY=${KLINK_DMS_APP_KEY}
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
        TUSUPLOAD_USE_PROXY=true
        TUSUPLOAD_HOST=0.0.0.0
        TUSUPLOAD_HTTP_PATH=/tus-uploads/
        TUSUPLOAD_URL=${KLINK_DMS_APP_URL}tus-uploads/
	EOM
	echo "- ENV file written! $KLINK_DMS_DIR/.env"
}

function update_dms() {
    cd ${KLINK_DMS_DIR} &&
    echo "- Launching dms:update procedure..." &&
    php artisan dms:update --no-test -vv  &&
    # su -s /bin/sh -c "php artisan dms:update --no-test -vv" $KLINK_SETUP_WWWUSER &&
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

    cp $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/pre-create $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/pre-create-original \
    && cp $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-receive $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-receive-original \
    && cp $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-finish $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-finish-original \
    && cp $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-terminate $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-terminate-original \

    tr -d '\r' < $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/pre-create-original > $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/pre-create
    tr -d '\r' < $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-receive-original > $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-receive
    tr -d '\r' < $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-finish-original > $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-finish
    tr -d '\r' < $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-terminate-original > $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/post-terminate

    chgrp -R $KLINK_SETUP_WWWUSER $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/ \
    && chmod -R +x $KLINK_DMS_DIR/vendor/avvertix/laravel-tus-upload/hooks/linux/
}

function create_admin () {
    su -s /bin/sh -c "php artisan dms:create-admin '$KLINK_DMS_ADMIN_USERNAME' '$KLINK_DMS_ADMIN_PASSWORD'" $KLINK_SETUP_WWWUSER

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

startup_config >&2
