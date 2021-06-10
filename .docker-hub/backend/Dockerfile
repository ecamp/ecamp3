# composer stage
FROM composer:2@sha256:58615464915bec995bdbf50354b8ab8d509f238ed7137755d08b595cc2afd726 AS composer-stage
WORKDIR /app
ENV env=prod
COPY backend/composer.* ./
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist
COPY backend .
RUN composer di-generate-aot

# production stage
FROM php:7.4.19-apache@sha256:95b36612c935bde6a41a7ed11b706c8abc99d8f85e2c17854168a40167da6537 AS production-stage
WORKDIR /app
ENV env=prod
RUN apt-get -y update && apt-get -y upgrade && apt-get -y install libxml2-dev curl unzip iproute2 libonig-dev && docker-php-ext-install pdo pdo_mysql xml
COPY --from=composer-stage /app/vendor /app/vendor
COPY backend .
RUN chmod -R a+rw data && a2enmod rewrite
COPY .docker-hub/backend/apache-vhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY .docker-hub/backend/docker-entrypoint.sh docker-entrypoint.sh
ENTRYPOINT bash docker-entrypoint.sh
