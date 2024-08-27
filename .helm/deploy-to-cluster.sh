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
domain="${domain:-ecamp3.ch}"
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
  values="$values --set termsOfServiceLinkTemplate=https://ecamp3.ch/{lang}/tos"
  values="$values --set newsLink=https://ecamp3.ch/blog"
  values="$values --set helpLink=https://ecamp3.ch/faq"
  values="$values --set domain=$instance_name-"$i".$domain"
  values="$values --set mail.dummyEnabled=true"
  values="$values --set ingress.basicAuth.enabled=$BASIC_AUTH_ENABLED"
  values="$values --set ingress.basicAuth.username=$BASIC_AUTH_USERNAME"
  values="$values --set ingress.basicAuth.password=$BASIC_AUTH_PASSWORD"
  values="$values --set apiCache.enabled=$API_CACHE_ENABLED"
  values="$values --set postgresql.enabled=false"
  values="$values --set postgresql.url=$POSTGRES_URL/ecamp3$instance_name-"$i"?sslmode=require"
  values="$values --set postgresql.adminUrl=$POSTGRES_ADMIN_URL/ecamp3$instance_name-"$i"?sslmode=require"
  values="$values --set postgresql.dropDBOnUninstall=true"
  values="$values --set api.dataMigrationsDir=$migrations_dir"
  values="$values --set api.appSecret=$app_secret"
  if [ -n "$API_SENTRY_DSN" ]; then
    values="$values --set api.sentryDsn=$API_SENTRY_DSN"
  fi
  if [ -n "$FRONTEND_SENTRY_DSN" ]; then
    values="$values --set frontend.sentryDsn=$FRONTEND_SENTRY_DSN"
  fi
  if [ -n "$PRINT_SENTRY_DSN" ]; then
    values="$values --set print.sentryDsn=$PRINT_SENTRY_DSN"
  fi
  values="$values --set api.jwt.passphrase=$app_jwt_passphrase"
  values="$values --set-file api.jwt.publicKey=$SCRIPT_DIR/public.pem"
  values="$values --set-file api.jwt.privateKey=$SCRIPT_DIR/private.pem"
  values="$values --set deploymentTime=$(date -u +%s)"
  values="$values --set deployedVersion=\"$(git rev-parse --short HEAD)\""
  values="$values --set featureToggle.developer=true"
  values="$values --set featureToggle.checklist=true"

  if [ -n "$BACKUP_SCHEDULE" ]; then
    values="$values --set postgresql.backup.schedule=$BACKUP_SCHEDULE"
    values="$values --set postgresql.backup.s3.endpoint=$BACKUP_S3_ENDPOINT"
    values="$values --set postgresql.backup.s3.bucket=$BACKUP_S3_BUCKET"
    values="$values --set postgresql.backup.s3.accessKeyId=$BACKUP_S3_ACCESS_KEY_ID"
    values="$values --set postgresql.backup.s3.accessKey=$BACKUP_S3_ACCESS_KEY"
    if [ -n $BACKUP_ENCRYPTION_KEY ]; then
      values="$values --set postgresql.backup.encryptionKey=$BACKUP_ENCRYPTION_KEY"
    fi
  fi

  if [ -n "$RESTORE_SOURCE_FILE" ]; then
    values="$values --set postgresql.restore.sourceFile=$RESTORE_SOURCE_FILE"
    values="$values --set postgresql.restore.s3.endpoint=$RESTORE_S3_ENDPOINT"
    values="$values --set postgresql.restore.s3.bucket=$RESTORE_S3_BUCKET"
    values="$values --set postgresql.restore.s3.accessKeyId=$RESTORE_S3_ACCESS_KEY_ID"
    values="$values --set postgresql.restore.s3.accessKey=$RESTORE_S3_ACCESS_KEY"
    values="$values --set postgresql.restore.sourceAppName=$RESTORE_SOURCE_APP"
    if [ -n $RESTORE_ENCRYPTION_KEY ]; then
      values="$values --set postgresql.restore.encryptionKey=$RESTORE_ENCRYPTION_KEY"
    fi
    values="$values --set postgresql.restore.inviteSupportAccountToInterestingCamps=$RESTORE_INVITE_TO_INTERESTING_CAMPS"
  fi

  for imagespec in "frontend" "print" "api"; do
    values="$values --set $imagespec.image.pullPolicy=$pull_policy"
    values="$values --set $imagespec.image.repository=docker.io/${docker_hub_account}/ecamp3-$imagespec"
  done

  values="$values --set apiCache.image.repository=docker.io/${docker_hub_account}/ecamp3-varnish"

  values="$values --set postgresql.dbBackupRestoreImage.pullPolicy=$pull_policy"
  values="$values --set postgresql.dbBackupRestoreImage.repository=docker.io/${docker_hub_account}/ecamp3-db-backup-restore"

  helm uninstall ecamp3-"$instance_name"-"$i" || true
  helm upgrade --install ecamp3-"$instance_name"-"$i" $SCRIPT_DIR/ecamp3 $values
done

rm -f private.pem
rm -f public.pem
