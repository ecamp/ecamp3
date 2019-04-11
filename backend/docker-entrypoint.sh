#!/bin/bash

chmod -R a+rw data

# Add hostname of docker host for XDebug to connect to. This can be removed once host.docker.internal is also supported in Docker for Linux (currently is only Win and Mac)
if ! grep "host.docker.internal" /etc/hosts > /dev/null ; then
    DOCKER_INTERNAL_IP=`/sbin/ip -4 route list match 0/0 | awk '{ print $3 }' | head -n 1`
    echo -e "$DOCKER_INTERNAL_IP\thost.docker.internal" >> /etc/hosts
    echo "Added host.docker.internal to /etc/hosts"
fi

DB_CONFIG_FILE="config/autoload/doctrine.local.dev.php"
if [ ! -f "$DB_CONFIG_FILE" ]; then
    sed 's/127.0.0.1/db/g' config/autoload/doctrine.local.dev.dist > "$DB_CONFIG_FILE"
fi

php bin/wait-for-composer-install.php
php bin/wait-for-db.php
vendor/bin/doctrine orm:schema-tool:update --force --complete

apache2-foreground
