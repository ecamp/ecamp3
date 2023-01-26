#!/bin/bash

set -e

SCRIPT_DIR=$(realpath "$(dirname "$0")")
REPO_DIR=$(realpath "$SCRIPT_DIR"/..)

# docker hub
registry_uri=https://index.docker.io/v1/
not_logged_in="echo \"You need to login to the docker registry $registry_uri\" && exit 1"
jq -e ".auths | .\"${registry_name}\" | .auth" < ~/.docker/config.json || $not_logged_in

source "$SCRIPT_DIR"/.env

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
