#!/bin/bash

set -e

SCRIPT_DIR=$(realpath "$(dirname "$0")")
REPO_DIR=$(realpath "$SCRIPT_DIR"/..)

source "$SCRIPT_DIR"/.env

if [ -z "$docker_hub_account" ] \
    || [ -z "$version" ] \
    ; then
    echo "Please specify the needed env variables in $SCRIPT_DIR/.env"
    echo "An example can be seen here:"
    cat $SCRIPT_DIR/.env-example
    exit 1
fi

frontend_image_tag="${docker_hub_account}/ecamp3-frontend:${version}"
docker build "$REPO_DIR" -f "$REPO_DIR"/.docker-hub/frontend/Dockerfile -t "$frontend_image_tag"
docker push "$frontend_image_tag"

api_image_tag="${docker_hub_account}/ecamp3-php:${version}"
docker build "$REPO_DIR"/api -f "$REPO_DIR"/api/Dockerfile -t "$api_image_tag" --target api_platform_php
docker push "$api_image_tag"

caddy_image_tag="${docker_hub_account}/ecamp3-caddy:${version}"
docker build "$REPO_DIR"/api -f "$REPO_DIR"/api/Dockerfile -t "$caddy_image_tag" --target api_platform_caddy_prod
docker push "$caddy_image_tag"

print_image_tag="${docker_hub_account}/ecamp3-print:${version}"
docker build "$REPO_DIR" -f "$REPO_DIR"/.docker-hub/print/Dockerfile -t "$print_image_tag"
docker push "$print_image_tag"
