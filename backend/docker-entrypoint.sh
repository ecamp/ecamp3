#!/bin/bash

COMPOSER_ALLOW_SUPERUSER=1 composer install --ignore-platform-reqs --no-interaction --no-plugins --no-scripts --prefer-dist

DB_CONFIG_FILE="config/autoload/doctrine.local.dev.php"
if [ ! -f "$DB_CONFIG_FILE" ]; then
    sed 's/127.0.0.1/db/g' config/autoload/doctrine.local.dev.dist > "$DB_CONFIG_FILE"
fi
php bin/check-db-connection.php
vendor/bin/doctrine orm:schema-tool:create

apache2-foreground
