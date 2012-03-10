#!/bin/bash

if [ ! -d $DATAPATH ]; then
	mkdir -p $DATAPATH
fi

mysqldump -u $DBUSER -p$DBPASS --skip-dump-date --skip-comments $DB > $FILE

