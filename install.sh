#!/bin/bash

if command -v composer &> /dev/null; then
    composer_path=$(which composer)
else
    exit
fi

export COMPOSER_ALLOW_SUPERUSER=1

root_path=$(pwd)

domain=$1

config_file=/www/server/panel/vhost/nginx/${domain}.conf

php_version=$(cat $config_file|grep 'enable-php'|grep -Eo "[0-9]+"|head -n 1)

php_bin=/www/server/php/$php_version/bin/php

$php_bin $composer_path install --no-interaction --working-dir=$root_path
