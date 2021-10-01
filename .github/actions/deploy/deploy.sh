#!/bin/bash
set -e

# Calculate short commit id
COMMIT_ID=$(git rev-parse --short "$COMMIT_SHA")
DEPLOYMENT_TIME=$(date -u +%s)
# Extract the domains from the URLs
FRONTEND_DOMAIN=$(echo "$FRONTEND_URL" | sed -E 's~^(.*://)?(.*@)?([^:/]*).*$~\3~')
BACKEND_DOMAIN=$(echo "$BACKEND_URL" | sed -E 's~^(.*://)?(.*@)?([^:/]*).*$~\3~')
PRINT_SERVER_DOMAIN=$(echo "$PRINT_SERVER_URL" | sed -E 's~^(.*://)?(.*@)?([^:/]*).*$~\3~')
PRINT_FILE_SERVER_DOMAIN=$(echo "$PRINT_FILE_SERVER_URL" | sed -E 's~^(.*://)?(.*@)?([^:/]*).*$~\3~')
MAIL_SERVER_DOMAIN=$(echo "$MAIL_SERVER_URL" | sed -E 's~^(.*://)?(.*@)?([^:/]*).*$~\3~')

# Inject the container version into the .env file
cp .github/actions/deploy/.env .github/actions/deploy/dist/.env
sed -ri "s~DOCKER_IMAGE_TAG=.*~DOCKER_IMAGE_TAG=${COMMIT_SHA}~" .github/actions/deploy/dist/.env

# Inject environment secrets into nginx config file
cp .github/actions/deploy/nginx.conf .github/actions/deploy/dist/nginx.conf
sed -ri "s~server_name frontend-domain;~server_name ${FRONTEND_DOMAIN};~" .github/actions/deploy/dist/nginx.conf
sed -ri "s~server_name backend-domain;~server_name ${BACKEND_DOMAIN};~" .github/actions/deploy/dist/nginx.conf
sed -ri "s~server_name print-server-domain;~server_name ${PRINT_SERVER_DOMAIN};~" .github/actions/deploy/dist/nginx.conf
sed -ri "s~server_name print-file-server-domain;~server_name ${PRINT_FILE_SERVER_DOMAIN};~" .github/actions/deploy/dist/nginx.conf
sed -ri "s~server_name mail-server-domain;~server_name ${MAIL_SERVER_DOMAIN};~" .github/actions/deploy/dist/nginx.conf

# Inject environment secrets into backend config files
EDIT_SCRIPT=".github/actions/deploy/edit_array_file.php"
DB_CONFIG_FILE=".github/actions/deploy/dist/doctrine.local.prod.php"
cp backend/config/autoload/doctrine.docker.dist $DB_CONFIG_FILE
php $EDIT_SCRIPT $DB_CONFIG_FILE "doctrine.connection.orm_default.params.host" "${DB_HOST}"
php $EDIT_SCRIPT $DB_CONFIG_FILE "doctrine.connection.orm_default.params.port" "${DB_PORT}"
php $EDIT_SCRIPT $DB_CONFIG_FILE "doctrine.connection.orm_default.params.user" "${DB_USER}"
php $EDIT_SCRIPT $DB_CONFIG_FILE "doctrine.connection.orm_default.params.password" "${DB_PASS}"
php $EDIT_SCRIPT $DB_CONFIG_FILE "doctrine.connection.orm_default.params.dbname" "${DB_NAME}"
cp backend/config/autoload/mail.local.docker.dist .github/actions/deploy/dist/mail.local.prod.php
cp backend/config/autoload/sessions.local.docker.dist .github/actions/deploy/dist/sessions.local.prod.php
php $EDIT_SCRIPT .github/actions/deploy/dist/sessions.local.prod.php "session_config.cookie_domain" "${SESSION_COOKIE_DOMAIN}"
cp backend/config/autoload/zfr_cors.global.php .github/actions/deploy/dist/zfr_cors.global.php
php $EDIT_SCRIPT .github/actions/deploy/dist/zfr_cors.global.php "zfr_cors.allowed_origins.0" "${FRONTEND_URL}"
php $EDIT_SCRIPT .github/actions/deploy/dist/zfr_cors.global.php "zfr_cors.allowed_origins.1" "${PRINT_SERVER_URL}"
cp backend/config/sentry.config.php.dist .github/actions/deploy/dist/sentry.config.php
php $EDIT_SCRIPT .github/actions/deploy/dist/sentry.config.php "dsn" "${SENTRY_BACKEND_DSN}"
cp backend/config/autoload/amq.local.dev.dist .github/actions/deploy/dist/amq.local.prod.php
php $EDIT_SCRIPT .github/actions/deploy/dist/amq.local.prod.php "amqp.connection.host" "${RABBITMQ_HOST}"
php $EDIT_SCRIPT .github/actions/deploy/dist/amq.local.prod.php "amqp.connection.port" "${RABBITMQ_PORT}"
php $EDIT_SCRIPT .github/actions/deploy/dist/amq.local.prod.php "amqp.connection.vhost" "${RABBITMQ_VHOST}"
php $EDIT_SCRIPT .github/actions/deploy/dist/amq.local.prod.php "amqp.connection.user" "${RABBITMQ_USER}"
php $EDIT_SCRIPT .github/actions/deploy/dist/amq.local.prod.php "amqp.connection.pass" "${RABBITMQ_PASS}"

# Inject environment variables into print env file
cp api/.env .github/actions/deploy/dist/api.env
sed -ri "s~APP_ENV=.*~APP_ENV=dev~" .github/actions/deploy/dist/api.env
sed -ri "s~APP_SECRET=.*~APP_SECRET=${API_APP_SECRET}~" .github/actions/deploy/dist/api.env
sed -ri "s~DATABASE_URL=.*~DATABASE_URL=${API_DATABASE_URL}~" .github/actions/deploy/dist/api.env
sed -ri "s~JWT_PASSPHRASE=.*~JWT_PASSPHRASE=${API_JWT_PASSPHRASE}~" .github/actions/deploy/dist/api.env

# Inject environment variables into frontend config file
cp frontend/public/environment.dist .github/actions/deploy/dist/frontend-environment.js
sed -ri "s~API_ROOT_URL: '.*'~API_ROOT_URL: '${BACKEND_URL}'~" .github/actions/deploy/dist/frontend-environment.js
sed -ri "s~PRINT_SERVER: '.*'~PRINT_SERVER: '${PRINT_SERVER_URL}'~" .github/actions/deploy/dist/frontend-environment.js
sed -ri "s~PRINT_FILE_SERVER: '.*'~PRINT_FILE_SERVER: '${PRINT_FILE_SERVER_URL}'~" .github/actions/deploy/dist/frontend-environment.js
sed -ri "s~SENTRY_FRONTEND_DSN: .*~SENTRY_FRONTEND_DSN: '${SENTRY_FRONTEND_DSN}',~" .github/actions/deploy/dist/frontend-environment.js
sed -ri "s~DEPLOYMENT_TIME: '.*'~DEPLOYMENT_TIME: '${DEPLOYMENT_TIME}'~" .github/actions/deploy/dist/frontend-environment.js
sed -ri "s~VERSION: '.*'~VERSION: '${COMMIT_ID}'~" .github/actions/deploy/dist/frontend-environment.js
sed -ri "s~VERSION_LINK_TEMPLATE: '.*'~VERSION_LINK_TEMPLATE: '${VERSION_LINK_TEMPLATE}'~" .github/actions/deploy/dist/frontend-environment.js

# Inject environment variables into print env file
cp print/print.env .github/actions/deploy/dist/print.env
sed -ri "s~INTERNAL_API_ROOT_URL=.*~INTERNAL_API_ROOT_URL=${INTERNAL_BACKEND_URL}~" .github/actions/deploy/dist/print.env
sed -ri "s~API_ROOT_URL=.*~API_ROOT_URL=${BACKEND_URL}~" .github/actions/deploy/dist/print.env
sed -ri "s~FRONTEND_URL=.*~FRONTEND_URL=${FRONTEND_URL}~" .github/actions/deploy/dist/print.env
sed -ri "s~SENTRY_PRINT_DSN=.*~SENTRY_PRINT_DSN=${SENTRY_PRINT_DSN}~" .github/actions/deploy/dist/print.env

# Inject environment secrets into print-worker-puppeteer config file
cp workers/print-puppeteer/environment.js .github/actions/deploy/dist/worker-print-puppeteer-environment.js
sed -ri "s~PRINT_SERVER: .*$~PRINT_SERVER: '${PRINT_SERVER_URL}',~" .github/actions/deploy/dist/worker-print-puppeteer-environment.js
sed -ri "s~SESSION_COOKIE_DOMAIN: .*$~SESSION_COOKIE_DOMAIN: '${SESSION_COOKIE_DOMAIN}',~" .github/actions/deploy/dist/worker-print-puppeteer-environment.js
sed -ri "s~SENTRY_WORKER_PRINT_PUPPETEER_DSN: .*$~SENTRY_WORKER_PRINT_PUPPETEER_DSN: '${SENTRY_WORKER_PRINT_PUPPETEER_DSN}',~" .github/actions/deploy/dist/worker-print-puppeteer-environment.js
sed -ri "s~AMQP_HOST: .*$~AMQP_HOST: '${RABBITMQ_HOST}',~" .github/actions/deploy/dist/worker-print-puppeteer-environment.js
sed -ri "s~AMQP_PORT: .*$~AMQP_PORT: '${RABBITMQ_PORT}',~" .github/actions/deploy/dist/worker-print-puppeteer-environment.js
sed -ri "s~AMQP_VHOST: .*$~AMQP_VHOST: '${RABBITMQ_VHOST}',~" .github/actions/deploy/dist/worker-print-puppeteer-environment.js
sed -ri "s~AMQP_USER: .*$~AMQP_USER: '${RABBITMQ_USER}',~" .github/actions/deploy/dist/worker-print-puppeteer-environment.js
sed -ri "s~AMQP_PASS: .*$~AMQP_PASS: '${RABBITMQ_PASS}',~" .github/actions/deploy/dist/worker-print-puppeteer-environment.js

# Inject environment secrets into print-worker-weasy config file
cp workers/print-weasy/environment.py .github/actions/deploy/dist/worker-print-weasy-environment.py
sed -ri "s~PRINT_SERVER = .*$~PRINT_SERVER = '${PRINT_SERVER_URL}'~" .github/actions/deploy/dist/worker-print-weasy-environment.py
sed -ri "s~SENTRY_WORKER_PRINT_WEASY_DSN = .*$~SENTRY_WORKER_PRINT_WEASY_DSN = '${SENTRY_WORKER_PRINT_WEASY_DSN}'~" .github/actions/deploy/dist/worker-print-weasy-environment.py
sed -ri "s~AMQP_HOST = .*$~AMQP_HOST = '${RABBITMQ_HOST}'~" .github/actions/deploy/dist/worker-print-weasy-environment.py
sed -ri "s~AMQP_PORT = .*$~AMQP_PORT = '${RABBITMQ_PORT}'~" .github/actions/deploy/dist/worker-print-weasy-environment.py
sed -ri "s~AMQP_VHOST = .*$~AMQP_VHOST = '${RABBITMQ_VHOST}'~" .github/actions/deploy/dist/worker-print-weasy-environment.py
sed -ri "s~AMQP_USER = .*$~AMQP_USER = '${RABBITMQ_USER}'~" .github/actions/deploy/dist/worker-print-weasy-environment.py
sed -ri "s~AMQP_PASS = .*$~AMQP_PASS = '${RABBITMQ_PASS}'~" .github/actions/deploy/dist/worker-print-weasy-environment.py

# Inject environment secrets into rabbitmq env file
cp .github/actions/deploy/rabbitmq.env .github/actions/deploy/dist/rabbitmq.env
sed -ri "s~RABBITMQ_DEFAULT_USER=.*~RABBITMQ_DEFAULT_USER=${RABBITMQ_USER}~" .github/actions/deploy/dist/rabbitmq.env
sed -ri "s~RABBITMQ_DEFAULT_PASS=.*~RABBITMQ_DEFAULT_PASS=${RABBITMQ_PASS}~" .github/actions/deploy/dist/rabbitmq.env

echo "Deploying the project to the server..."

rsync -avz -e ssh --delete .github/actions/deploy/dist/ "${SSH_USERNAME}@${SSH_HOST}:${SSH_DIRECTORY}"
# shellcheck disable=SC2087
ssh -T "${SSH_USERNAME}@${SSH_HOST}" <<EOF
  cd ${SSH_DIRECTORY}
  docker system prune -f -a --volumes
  docker-compose pull && docker-compose down --volumes && docker-compose up -d
EOF

echo "Deployment complete."
