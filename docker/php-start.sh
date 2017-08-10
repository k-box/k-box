#!/bin/bash

/usr/local/bin/configure.sh &&
exec php-fpm -F
