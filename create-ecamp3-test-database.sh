#!/bin/sh

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" -d postgres <<-EOSQL
	    CREATE DATABASE "ecamp3test";
	    GRANT ALL PRIVILEGES ON DATABASE "ecamp3test" TO $POSTGRES_USER;
EOSQL
