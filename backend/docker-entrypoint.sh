#!/bin/bash

chmod -R a+rw data

DB_CONFIG_FILE="config/autoload/doctrine.local.dev.php"
if [ ! -f "$DB_CONFIG_FILE" ]; then
    sed 's/127.0.0.1/db/g' config/autoload/doctrine.local.dev.dist > "$DB_CONFIG_FILE"
fi

php bin/wait-for-composer-install.php
php bin/wait-for-db.php
vendor/bin/doctrine orm:schema-tool:update --force --complete

apache2-foreground
