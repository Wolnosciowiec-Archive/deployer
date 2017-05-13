#!/bin/bash

echo " >> Creating var/cache directory if not exists and setting up correct permissions"
mkdir /var/www/var/cache -p
chown www-data:www-data /var/www/var -R

echo " >> Setting correct permissions on configuration files"
touch /var/www/app/config/parameters.yml
chown www-data:www-data /var/www/app/config -R

echo " >> Setting up correct permissions for vendor directory"
mkdir /var/www/vendor -p
chown www-data:www-data /var/www/vendor /var/www/bin -R

echo " >> Setting up correct permissions for web directory"
chown www-data:www-data /var/www/web -R
