#!/bin/sh

set -e

SCRIPT_DIR=$(dirname $(realpath $0))
CURRENT_DATE=$(date +"%Y%m%d%I%M%p")

LAST_PHP_FILE=$(ls ${SCRIPT_DIR}/Version*.php | tail -1)
NEW_PHP_FILE=${SCRIPT_DIR}/Version${CURRENT_DATE}.php

cp ${LAST_PHP_FILE} ${NEW_PHP_FILE}
sed -i "s/Version[0-9]*/Version${CURRENT_DATE}/" ${NEW_PHP_FILE}
# remove the lines between //START PHP CODE and //END PHP Code in ${LAST_PHP_FILE}
sed -i '/\/\/ START PHP CODE/,/\/\/ END PHP CODE/{/\/\/ START PHP CODE/!{/\/\/ END PHP CODE/!d}}' ${LAST_PHP_FILE}

docker-compose exec database pg_dump \
                                -U ecamp3 \
                                -d ecamp3dev \
                                -a \
                                --column-inserts \
                                --exclude-table=doctrine_migration_versions \
                                --exclude-table=content_type \
                                --rows-per-insert 10000 \
  | grep -v -e "pg_dump" \
  | grep -v "^--" \
  | grep -v "pg_dump" \
  | grep -v "SET" \
  | grep -v "SELECT pg_catalog" \
  | dos2unix \
  > ${SCRIPT_DIR}/data.sql
