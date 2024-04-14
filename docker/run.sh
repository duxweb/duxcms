#!/bin/sh

volume_path="/var/www/html/config/"
temp_path="/var/www/html/tmp/config/*"

cp -r -n ${temp_path} ${volume_path}

chown -R www-data:www-data /var/www/html/data
chown -R www-data:www-data /var/www/html/config
chmod -R 777 /var/www/html/data
chmod -R 777 /var/www/html/config

echo 'Initialization Configuration Successful'

supervisord -c /etc/supervisord.conf