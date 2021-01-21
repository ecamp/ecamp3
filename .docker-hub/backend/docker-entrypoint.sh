#!/bin/bash
set -euo pipefail

php bin/wait-for-db.php
vendor/bin/laminas rebuild-database
vendor/bin/laminas load-data-fixtures --path=module/eCampCore/data/*
rm -rf data/DoctrineORMModule

apache2-foreground
