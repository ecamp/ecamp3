#!/bin/bash

DB_CONFIG_FILE="config/autoload/doctrine.local.dev.php"
if [ ! -f "$DB_CONFIG_FILE" ]; then
    sed 's/127.0.0.1/db/g' config/autoload/doctrine.local.dev.dist > "$DB_CONFIG_FILE"
fi

composer install --ignore-platform-reqs --no-interaction --no-plugins --no-scripts --prefer-dist --no-suggest

composer di-generate-aot
