#!/bin/bash

mysql -u $DBUSER -p$DBPASS $DB -e "show tables" | grep -v Tables_in | grep -v "+" | sed 's/^/drop table /g' | sed 's/$/;/' | sed '1s/^/SET foreign_key_checks = 0; \ /g' | mysql -u $DBUSER -p$DBPASS $DB