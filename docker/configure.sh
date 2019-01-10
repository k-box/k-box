#!/bin/bash

## The public URL on which the K-Box will be available
KBOX_APP_URL=${KBOX_APP_URL:-${KLINK_DMS_APP_URL:-}}
## The URL on which the K-Box will be reachable in the Docker network. Default http://kbox/
KBOX_APP_LOCAL_URL=${KBOX_APP_LOCAL_URL:-${KLINK_DMS_APP_INTERNAL_URL:-http://kbox/}}
## Application key
KBOX_APP_KEY=${KBOX_APP_KEY:-${KLINK_DMS_APP_KEY:-}}
## Application environment name
KBOX_APP_ENV=${KBOX_APP_ENV:-${KLINK_DMS_APP_ENV:-production}}
## Enable/Disable the debug mode
KBOX_APP_DEBUG=${KBOX_APP_DEBUG:-${KLINK_DMS_APP_DEBUG:-false}}

## Maximum file size for upload (KB)
KBOX_UPLOAD_LIMIT=${KBOX_UPLOAD_LIMIT:-${KLINK_DMS_MAX_UPLOAD_SIZE:-204800}}

## Database connection
KBOX_DB_NAME=${KBOX_DB_NAME:-${KLINK_DMS_DB_NAME:-dms}}
KBOX_DB_HOST=${KBOX_DB_HOST:-${KLINK_DMS_DB_HOST:-127.0.0.1}}
KBOX_DB_USERNAME=${KBOX_DB_USERNAME:-${KLINK_DMS_DB_USERNAME:-dms}}
KBOX_DB_PASSWORD=${KBOX_DB_PASSWORD:-${KLINK_DMS_DB_PASSWORD}}
KBOX_DB_TABLE_PREFIX=${KBOX_DB_TABLE_PREFIX:-${KLINK_DMS_DB_TABLE_PREFIX:-kdms_}}

## Administration account
KBOX_ADMIN_USERNAME=${KBOX_ADMIN_USERNAME:-${KLINK_DMS_ADMIN_USERNAME:-}}
KBOX_ADMIN_PASSWORD=${KBOX_ADMIN_PASSWORD:-${KLINK_DMS_ADMIN_PASSWORD:-}}

## Search service endpoint
KBOX_SEARCH_SERVICE_URL=${KBOX_SEARCH_SERVICE_URL:-${KLINK_DMS_CORE_ADDRESS:-http://ksearch.local/}}

## Trigger privacy policy creation from templates
KBOX_LOAD_PRIVACY=${KBOX_LOAD_PRIVACY:-false}

## Variables required by the configure script
## The Institution identifier (deprecated)
KLINK_CORE_ID=${KLINK_CORE_ID:-KLINK}
## User under which the commands will run
KBOX_SETUP_USER=www-data
## Directory where the code is located
KBOX_DIR=/var/www/dms

function startup_config () {
    echo "Configuring K-Box..."
    echo "- Writing php configuration..."

    if [ -z "$KBOX_PHP_POST_MAX_SIZE" ]; then
        # calculating the post max size based on the upload limit
        KBOX_PHP_POST_MAX_SIZE_CALCULATION=$((KBOX_UPLOAD_LIMIT+20048))
        KBOX_PHP_POST_MAX_SIZE="${KBOX_PHP_POST_MAX_SIZE_CALCULATION}K"
    fi

    if [ -z "$KBOX_PHP_UPLOAD_MAX_FILESIZE" ]; then
        # calculating the upload max filesize based on the upload limit
        KBOX_PHP_UPLOAD_MAX_FILESIZE_CALCULATION=$((KBOX_UPLOAD_LIMIT+2048))
        KBOX_PHP_UPLOAD_MAX_FILESIZE="${KBOX_PHP_UPLOAD_MAX_FILESIZE_CALCULATION}K"
    fi
    
    # Set post and upload size for php if customized for the specific deploy
    cat > /usr/local/etc/php/conf.d/php-runtime.ini <<-EOM &&
		post_max_size=${KBOX_PHP_POST_MAX_SIZE}
        upload_max_filesize=${KBOX_PHP_UPLOAD_MAX_FILESIZE}
        memory_limit=${KBOX_PHP_MEMORY_LIMIT}
        max_input_time=${KBOX_PHP_MAX_INPUT_TIME}
        max_execution_time=${KBOX_PHP_MAX_EXECUTION_TIME}
	EOM

    write_config &&
    init_empty_dir $KBOX_DIR/storage && 
    echo "Changing folder groups and permissions" &&
    chgrp -R $KBOX_SETUP_USER $KBOX_DIR/storage &&
    chgrp -R $KBOX_SETUP_USER $KBOX_DIR/bootstrap/cache &&
    chgrp -R $KBOX_SETUP_USER $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/bin/ &&
    chmod +x $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/bin/tusd-linux &&
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

    if [ -n "$KBOX_FLAGS" ]; then
        # enabling the flags for experimental features
        echo "K-Box is enabling the required flags..."
        su -s /bin/sh -c "php artisan flags '$KBOX_FLAGS'" $KBOX_SETUP_USER
    fi
    
    if [ -n "$KBOX_LOAD_PRIVACY" ] && [ "$KBOX_LOAD_PRIVACY" == true ]; then
        # trigger the privacy policy creation from templates
        echo "K-Box is creating the privacy policy from templates..."
        su -s /bin/sh -c "php artisan privacy:load" $KBOX_SETUP_USER
    fi
}

function write_config() {

    if [ -z "$KBOX_APP_URL" ]; then
        # application URL not set
        echo "**************"
        echo "K-Box public URL not set. Set the public URL using KBOX_APP_URL."
        echo "**************"
        return 240
    fi

    echo "- Writing env file..."

	cat > ${KBOX_DIR}/.env <<-EOM &&
		APP_KEY=${KBOX_APP_KEY}
		APP_URL=${KBOX_APP_URL}
		APP_ENV=${KBOX_APP_ENV}
		APP_DEBUG=${KBOX_APP_DEBUG}
		DMS_INSTITUTION_IDENTIFIER=${KLINK_CORE_ID}
		APP_INTERNAL_URL=${KBOX_APP_LOCAL_URL}
		DMS_CORE_ADDRESS=${KBOX_SEARCH_SERVICE_URL}
		UPLOAD_LIMIT=${KBOX_UPLOAD_LIMIT}
		KBOX_DB_NAME=${KBOX_DB_NAME}
		KBOX_DB_HOST=${KBOX_DB_HOST}
		KBOX_DB_USERNAME=${KBOX_DB_USERNAME}
		KBOX_DB_PASSWORD=${KBOX_DB_PASSWORD}
		KBOX_DB_TABLE_PREFIX=${KBOX_DB_TABLE_PREFIX}
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
    cd ${KBOX_DIR} || return 242
    echo "- Launching dms:update procedure..."
    php artisan dms:update --no-test -vv
    create_admin
}

function wait_mariadb () {
    wait_command mariadb_test 6 10
}

function mariadb_test () {
   php -f /usr/local/bin/db-connect-test.php -- -d "${KBOX_DB_NAME}" -H "${KBOX_DB_HOST}" -u "${KBOX_DB_USERNAME}" -p "${KBOX_DB_PASSWORD}"
}

function wait_command () {
    local command=$1
    local retry_times=$2
    local sleep_seconds=$3

    for i in $(seq "$retry_times"); do
        echo "- Waiting for ${command} ... Retry $i"
        if [[ "$command" -eq 0 ]]; then
            return 0
        else
            sleep "$sleep_seconds"
        fi
    done
    return 1
}

function normalize_line_endings() {

    cp $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/pre-create $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/pre-create-original \
    && cp $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-finish $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-finish-original \
    && cp $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-terminate $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-terminate-original \

    tr -d '\r' < $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/pre-create-original > $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/pre-create
    tr -d '\r' < $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-finish-original > $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-finish
    tr -d '\r' < $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-terminate-original > $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-terminate

    ## Limiting PHP resource usage by not processing the post-receive hook. 
    ## This will make the K-Box unaware of the upload status, but will 
    ## prevent uncontrolled php process creation if the chunk size is very small
    # tr -d '\r' < $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-receive-original > $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-receive
    # && cp $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-receive $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-receive-original \
    mv $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-receive $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-receive.prevented

    chgrp -R $KBOX_SETUP_USER $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/ \
    && chmod -R +x $KBOX_DIR/vendor/oneofftech/laravel-tus-upload/hooks/linux/
}

function create_admin () {

    if [ -z "$KBOX_ADMIN_USERNAME" ] &&  [ -z "$KBOX_ADMIN_PASSWORD" ]; then
        # if both username and password are not defined or empty, tell to create the user afterwards an end return
        echo "**************"
        echo "Remember to create an admin user: php artisan create-admin --help"
        echo "**************"
        return 0
    fi

    if [ -z "$KBOX_ADMIN_USERNAME" ] &&  [ -n "$KBOX_ADMIN_PASSWORD" ]; then
        # username not set, but password set => error
        echo "**************"
        echo "Admin email not specified. Please specify an email address using the variable KBOX_ADMIN_USERNAME"
        echo "**************"
        return 240
    fi
    
    if [ -n "$KBOX_ADMIN_USERNAME" ] &&  [ -z "$KBOX_ADMIN_PASSWORD" ]; then
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
