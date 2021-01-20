#!/bin/bash

php bin/wait-for-db.php
vendor/bin/laminas rebuild-database
vendor/bin/laminas load-data-fixture --path=module/eCampCore/data/*

apache2-foreground
