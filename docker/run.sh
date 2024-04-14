#!/bin/sh

volume_path="/var/www/html/config/"
temp_path="/var/www/html/tmp/config/*"

cp -r -n ${temp_path} ${volume_path}

echo 'Initialization Configuration Successful'

supervisord -c /etc/supervisord.conf