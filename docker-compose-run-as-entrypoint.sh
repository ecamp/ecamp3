#!/bin/sh

if [ $# -lt 2 ]; then
  echo "Usage: $0 <service> <command> [<option>...] [<param>...]"
  exit 1
fi
service=$1
shift
entrypoint=$@
switch_user=
if [ "$CI" = "true" ]; then
  switch_user="--user 1001"
fi
docker-compose run --rm --no-deps $switch_user --entrypoint="$entrypoint" $service
