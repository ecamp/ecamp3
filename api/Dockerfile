#syntax=docker/dockerfile:1.9

# Adapted from https://github.com/api-platform/api-platform/blob/fa1c5808305d7cadbf7b8392e0fddb6e80fb2092/api/Dockerfile

# Versions
FROM dunglas/frankenphp:1.2-php8.3.11 AS frankenphp_upstream

# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target

# Base FrankenPHP image
FROM frankenphp_upstream AS frankenphp_base

WORKDIR /app

# persistent / runtime deps
# hadolint ignore=DL3008
RUN apt-get update && apt-get install --no-install-recommends -y \
	acl \
	file \
	gettext \
	git \
	patch \
	&& rm -rf /var/lib/apt/lists/*

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/tmp/composer
ENV COMPOSER_CACHE_DIR=/tmp/composer/cache
RUN mkdir -p /tmp/composer/cache
RUN chmod ugo+w /tmp/composer/cache

RUN set -eux; \
	install-php-extensions \
	@composer \
	intl \
	zip \
	apcu \
	opcache \
	;

###> recipes ###
###> doctrine/doctrine-bundle ###
RUN set -eux; \
	install-php-extensions pdo_pgsql
###< doctrine/doctrine-bundle ###
###< recipes ###

COPY --link docker/php/conf.d/api-platform.ini $PHP_INI_DIR/conf.d/
ENV LOG_LEVEL=info
COPY --link docker/caddy/Caddyfile /etc/caddy/Caddyfile
COPY --link --chmod=755 docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --link --chmod=755 docker/php/migrate-database.sh /usr/local/bin/migrate-database

ENTRYPOINT ["docker-entrypoint"]

HEALTHCHECK --start-period=60s CMD curl -f http://localhost:2019/metrics || exit 1
CMD [ "frankenphp", "run", "--config", "/etc/caddy/Caddyfile" ]

# Dev FrankenPHP image
FROM frankenphp_base AS frankenphp_dev
ENV APP_ENV=dev XDEBUG_MODE=off

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY --link docker/php/conf.d/api-platform.dev.ini $PHP_INI_DIR/conf.d/

# renovate: datasource=github-tags depName=xdebug/xdebug
ARG XDEBUG_VERSION=3.3.2
RUN set -eux; \
	install-php-extensions \
	xdebug-$XDEBUG_VERSION \
	pcov \
	;

CMD [ "frankenphp", "run", "--config", "/etc/caddy/Caddyfile", "--watch" ]

# Prod FrankenPHP image
FROM frankenphp_base AS frankenphp_prod

ENV APP_ENV=prod
#ENV FRANKENPHP_CONFIG="import worker.Caddyfile"

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --link docker/php/conf.d/api-platform.prod.ini $PHP_INI_DIR/conf.d/
#COPY --link docker/caddy/worker.Caddyfile /etc/caddy/worker.Caddyfile

# prevent the reinstallation of vendors at every changes in the source code
COPY --link composer.* symfony.* ./
COPY --link patch patch/
RUN set -eux; \
	composer install --no-cache --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

# copy only specifically what we need
COPY --link .env ./
COPY --link bin bin/
COPY --link config config/
COPY --link migrations migrations/
COPY --link fixtures fixtures/
COPY --link public public/
COPY --link src src/
COPY --link templates templates/
COPY --link translations translations/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync;


