#!/bin/bash

cd /var/www

export SYMFONY_ENV=prod

if ! su www-data -s /bin/bash -c "composer install --no-dev"; then
    echo " > Composer deployment failed, exiting..."
    exit 1
fi