#!/bin/bash

composer install --ignore-platform-reqs --no-interaction --no-plugins --no-scripts --prefer-dist

DB_CONFIG_FILE="config/autoload/doctrine.local.dev.php"
if [ ! -f "$DB_CONFIG_FILE" ]; then
    sed 's/127.0.0.1/db/g' config/autoload/doctrine.local.dev.dist > "$DB_CONFIG_FILE"
fi
php bin/wait-for-db.php
vendor/bin/doctrine orm:schema-tool:update --force --complete

mkdir -p data/DoctrineORMModule/Proxy
chmod a+rw data/DoctrineORMModule/Proxy

apache2-foreground
