#!/bin/bash

DB_CONFIG_FILE="config/autoload/doctrine.local.dev.php"
if [ ! -s "$DB_CONFIG_FILE" ]; then
    sed 's/127.0.0.1/db/g' config/autoload/doctrine.local.dev.dist > "$DB_CONFIG_FILE"
fi
php bin/wait-for-db.php
vendor/bin/doctrine orm:schema-tool:update --force --complete

apache2-foreground
