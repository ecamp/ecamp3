# composer stage
FROM composer:2@sha256:447516936298f9f63d0b718d1a21bd70066b6c206e174cd29938c79b8eefe7b1 AS composer-stage
WORKDIR /app
ENV env=prod
COPY backend/composer.* ./
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist --no-suggest
COPY backend .
RUN composer di-generate-aot

# production stage
FROM php:7.4.14-apache@sha256:ba7c444c30b7e13ac5bafd212fc8b995c04a9b3762223df0570bb277b5842139 AS production-stage
WORKDIR /app
ENV env=prod
RUN apt-get -y update && apt-get -y upgrade && apt-get -y install libxml2-dev curl unzip iproute2 libonig-dev && docker-php-ext-install pdo pdo_mysql xml
COPY --from=composer-stage /app/vendor /app/vendor
COPY backend .
RUN chmod -R a+rw data && a2enmod rewrite
COPY .docker-hub/backend/apache-vhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY .docker-hub/backend/docker-entrypoint.sh docker-entrypoint.sh
ENTRYPOINT bash docker-entrypoint.sh
