#syntax=docker/dockerfile:1.7

# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact

# renovate: datasource=docker depName=php
ARG PHP_VERSION=8.3.4

# renovate: datasource=docker depName=alpine
ARG ALPINE_VERSION=3.18

# renovate: datasource=docker depName=caddy
ARG CADDY_VERSION=2.7.6

# "php" stage
FROM php:${PHP_VERSION}-fpm-alpine${ALPINE_VERSION} AS api_platform_php

# build for production
ARG APP_ENV=prod

WORKDIR /srv/api

# php extensions installer: https://github.com/mlocati/docker-php-extension-installer
COPY --from=mlocati/php-extension-installer --link /usr/bin/install-php-extensions /usr/local/bin/

# persistent / runtime deps
RUN apk add --no-cache \
	acl \
	fcgi \
	file \
	gettext \
	git \
	gnu-libiconv \
	patch \
	;

# install gnu-libiconv and set LD_PRELOAD env to make iconv work fully on Alpine image.
# see https://github.com/docker-library/php/issues/240#issuecomment-763112749
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

RUN set -eux; \
	install-php-extensions \
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

# Copy development php.ini
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --link docker/php/conf.d/api-platform.ini $PHP_INI_DIR/conf.d/
COPY --link docker/php/conf.d/api-platform.prod.ini $PHP_INI_DIR/conf.d/

COPY --link docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
COPY --link docker/php/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN mkdir -p /var/run/php

COPY --link docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

COPY --link docker/php/migrate-database.sh /usr/local/bin/migrate-database
RUN chmod +x /usr/local/bin/migrate-database

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"
ENV COMPOSER_HOME=/tmp/composer
ENV COMPOSER_CACHE_DIR=/tmp/composer/cache
RUN mkdir -p /tmp/composer/cache
RUN chmod ugo+w /tmp/composer/cache

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# prevent the reinstallation of vendors at every changes in the source code
COPY composer.json composer.lock symfony.lock ./
COPY --link patch patch/
RUN set -eux; \
	composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress; \
	composer clear-cache

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
	chmod +x bin/console; sync


# Debug stage, for using XDebug
FROM api_platform_php as api_platform_php_dev
ENV APP_ENV=dev XDEBUG_MODE=off

RUN rm $PHP_INI_DIR/conf.d/api-platform.prod.ini; \
	mv "$PHP_INI_DIR/php.ini" "$PHP_INI_DIR/php.ini-production"; \
	mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY --link docker/php/conf.d/api-platform.dev.ini $PHP_INI_DIR/conf.d/

# renovate: datasource=github-tags depName=xdebug/xdebug
ARG XDEBUG_VERSION=3.3.1
RUN set -eux; \
	install-php-extensions \
	xdebug-$XDEBUG_VERSION \
	pcov \
	;

# "caddy" stage
# depends on the "php" stage above
FROM caddy:${CADDY_VERSION} AS api_platform_caddy

WORKDIR /srv/api

COPY --from=api_platform_php --link /srv/api/public public/
COPY --link docker/caddy/Caddyfile /etc/caddy/Caddyfile

FROM api_platform_caddy AS api_platform_caddy_prod
COPY --link docker/caddy/Caddyfile.prod /etc/caddy/Caddyfile


