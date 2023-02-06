#!/bin/bash

set -e

SCRIPT_DIR=$(realpath "$(dirname "$0")")
REPO_DIR=$(realpath "$SCRIPT_DIR"/..)

source "$SCRIPT_DIR"/.env

if [ -z "$version" ] \
    || [ -z "$instance_name" ] \
    || [ -z "$POSTGRES_URL" ] \
    || [ -z "$POSTGRES_ADMIN_URL" ] \
    ; then
    echo "Please specify the needed env variables in $SCRIPT_DIR/.env"
    echo "An example can be seen here:"
    cat $SCRIPT_DIR/.env-example
    exit 1
fi

pull_policy="Always"
domain="ecamp3.ch"
values="--set imageTag=${version}"
migrations_dir="dev-data"

#generated values
app_secret=$(uuidgen)
app_jwt_passphrase=$(uuidgen)
app_jwt_public_key=$(echo -n "$app_jwt_passphrase" | openssl genpkey -out "$SCRIPT_DIR"/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096)
app_jwt_private_key=$(echo -n "$app_jwt_passphrase" | openssl pkey -in "$SCRIPT_DIR"/private.pem -passin stdin -out "$SCRIPT_DIR"/public.pem -pubout)

#secrets POSTGRES_URL, POSTGRES_ADMIN_URL and sentry dsn are in .env

#use 1 2 instead of "1" to deploy multiple deployments
for i in 1; do
  values="$values --set imageTag=${version}"
  values="$values --set sharedCookieDomain=.$domain"
  values="$values --set termsOfServiceLinkTemplate=https://ecamp3.ch/{lang}/tos"
  values="$values --set api.domain=api-$instance_name-"$i".$domain"
  values="$values --set frontend.domain=app-$instance_name-"$i".$domain"
  values="$values --set print.domain=print-$instance_name-"$i".$domain"
  values="$values --set mail.dummyEnabled=true"
  values="$values --set mail.domain=mail-$instance_name-"$i".$domain"
  values="$values --set postgresql.enabled=false"
  values="$values --set postgresql.url=$POSTGRES_URL/ecamp3$instance_name-"$i"?sslmode=require"
  values="$values --set postgresql.adminUrl=$POSTGRES_ADMIN_URL/ecamp3$instance_name-"$i"?sslmode=require"
  values="$values --set postgresql.dropDBOnUninstall=true"
  values="$values --set php.dataMigrationsDir=$migrations_dir"
  values="$values --set php.appSecret=$app_secret"
  #values="$values --set php.sentryDsn=$API_SENTRY_DSN"
  #values="$values --set frontend.sentryDsn=$FRONTEND_SENTRY_DSN"
  #values="$values --set print.sentryDsn=$PRINT_SENTRY_DSN"
  values="$values --set php.jwt.passphrase=$app_jwt_passphrase"
  values="$values --set-file php.jwt.publicKey=$SCRIPT_DIR/public.pem"
  values="$values --set-file php.jwt.privateKey=$SCRIPT_DIR/private.pem"
  values="$values --set deploymentTime=$(date -u +%s)"
  values="$values --set deployedVersion=\"$(git rev-parse --short HEAD)\""

  for imagespec in "frontend" "php" "caddy" "print"; do
    values="$values --set $imagespec.image.pullPolicy=$pull_policy"
    values="$values --set $imagespec.image.repository=docker.io/${docker_hub_account}/ecamp3-$imagespec"
  done

  helm uninstall ecamp3-"$instance_name"-"$i" || true
  helm upgrade --install ecamp3-"$instance_name"-"$i" $SCRIPT_DIR/ecamp3 $values
done

rm -f private.pem
rm -f public.pem
