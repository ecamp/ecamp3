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

sentry_build_args="--build-arg SENTRY_AUTH_TOKEN=$SENTRY_AUTH_TOKEN --build-arg SENTRY_ORG=$SENTRY_ORG"
sentry_build_args="$sentry_build_args --build-arg SENTRY_RELEASE_NAME=$SENTRY_RELEASE_NAME"

frontend_sentry_build_args="$sentry_build_args --build-arg SENTRY_FRONTEND_PROJECT=$SENTRY_FRONTEND_PROJECT"

frontend_image_tag="${docker_hub_account}/ecamp3-frontend:${version}"
docker build "$REPO_DIR" -f "$REPO_DIR"/.docker-hub/frontend/Dockerfile $frontend_sentry_build_args -t "$frontend_image_tag"
docker push "$frontend_image_tag"

api_image_tag="${docker_hub_account}/ecamp3-api-php:${version}"
docker build "$REPO_DIR"/api -f "$REPO_DIR"/api/Dockerfile -t "$api_image_tag" --target api_platform_php $sentry_build_args
docker push "$api_image_tag"

caddy_image_tag="${docker_hub_account}/ecamp3-api-caddy:${version}"
docker build "$REPO_DIR"/api -f "$REPO_DIR"/api/Dockerfile -t "$caddy_image_tag" --target api_platform_caddy_prod
docker push "$caddy_image_tag"

print_sentry_build_args="$sentry_build_args --build-arg SENTRY_PRINT_PROJECT=$SENTRY_PRINT_PROJECT"

print_image_tag="${docker_hub_account}/ecamp3-print:${version}"
docker build "$REPO_DIR" -f "$REPO_DIR"/.docker-hub/print/Dockerfile $print_sentry_build_args -t "$print_image_tag"
docker push "$print_image_tag"

varnish_image_tag="${docker_hub_account}/ecamp3-varnish:${version}"
docker build "$REPO_DIR" -f "$REPO_DIR"/.docker-hub/varnish/Dockerfile -t "$varnish_image_tag"
docker push "$varnish_image_tag"

export REPO_OWNER=${docker_hub_account}
export VERSION=${version}
db_backup_restore_docker_compose_path="$REPO_DIR"/.helm/ecamp3/files/db-backup-restore-image/docker-compose.yml
docker compose -f "$db_backup_restore_docker_compose_path" build
docker compose -f "$db_backup_restore_docker_compose_path" push
