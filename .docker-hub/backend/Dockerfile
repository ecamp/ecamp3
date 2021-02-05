# composer stage
FROM composer:2@sha256:97ca3398b5f320babe8ae7fe931c6248cac42ca04919cd9b54986263819ba6a7 AS composer-stage
WORKDIR /app
ENV env=prod
COPY backend/composer.* ./
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist --no-suggest
COPY backend .
RUN composer di-generate-aot

# production stage
FROM php:7.4.15-apache@sha256:5b4b0a0c6b4c48eadbaa6a0d76be792f8468e7b7c53f652342a4c50f119b1d42 AS production-stage
WORKDIR /app
ENV env=prod
RUN apt-get -y update && apt-get -y upgrade && apt-get -y install libxml2-dev curl unzip iproute2 libonig-dev && docker-php-ext-install pdo pdo_mysql xml
COPY --from=composer-stage /app/vendor /app/vendor
COPY backend .
RUN chmod -R a+rw data && a2enmod rewrite
COPY .docker-hub/backend/apache-vhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY .docker-hub/backend/docker-entrypoint.sh docker-entrypoint.sh
ENTRYPOINT bash docker-entrypoint.sh
