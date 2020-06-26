#!/bin/bash

BASEDIR=$(dirname "$0")
DB_CONFIG_FILE=$BASEDIR"/config/autoload/doctrine.local.dev.php"

if [ ! -f "$DB_CONFIG_FILE" ]; then
    cp $BASEDIR/config/autoload/doctrine.docker.dist "$DB_CONFIG_FILE"
fi

php bin/wait-for-composer-install.php
php bin/wait-for-db.php

# load schema, prod date & dev data
php cli-setup.php dev
