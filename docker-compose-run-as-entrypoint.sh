#!/bin/sh

if [ $# -lt 2 ]; then
  echo "Usage: $0 <service> <command> [<option>...] [<param>...]"
  exit 1
fi
service=$1
shift
entrypoint=$@
docker-compose run --rm --no-deps --entrypoint="$entrypoint" $service
