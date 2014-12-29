#!/bin/bash

basename="$(dirname $0)"
configFile="$basename/../config/redis/redis.local.conf"

if [ -f "$configFile" ]
then
	echo "Start Redis-Server with redis.local.conf"
else
	configFile="$basename/../config/redis/redis.base.conf"
	echo "$Start Redis-Server with redis.base.conf"
fi

redis-server $configFile