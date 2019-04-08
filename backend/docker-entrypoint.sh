#!/bin/bash

chmod -R a+rw data

php bin/wait-for-composer-install.php
php bin/wait-for-db.php
vendor/bin/doctrine orm:schema-tool:update --force --complete

apache2-foreground
