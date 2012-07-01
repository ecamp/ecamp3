#!/bin/bash

DATAPATH="data/sql"
FILE=$DATAPATH"/eCamp3dev.sql"

source bin/db/config.sh
source bin/db/clear.sh
source bin/db/runSql.sh
