#!/bin/bash

if [ -a $FILE ];
then
	mysql -u $DBUSER -p$DBPASS $DB < $FILE
fi
