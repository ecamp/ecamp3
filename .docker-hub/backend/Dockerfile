# composer stage
FROM composer:2@sha256:b3703ad1ca8e91a301c2653844633a9aa91734f3fb278c56e2745f58965713f3 AS composer-stage
WORKDIR /app
ENV env=prod
COPY backend/composer.* ./
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist
COPY backend .
RUN composer di-generate-aot

# production stage
FROM php:7.4.19-apache@sha256:b8faa4895a39ffbf8b9d274220c341aa1864e2498bc6ba530a52a36fc19a8b85 AS production-stage
WORKDIR /app
ENV env=prod
RUN apt-get -y update && apt-get -y upgrade && apt-get -y install libxml2-dev curl unzip iproute2 libonig-dev && docker-php-ext-install pdo pdo_mysql xml
COPY --from=composer-stage /app/vendor /app/vendor
COPY backend .
RUN chmod -R a+rw data && a2enmod rewrite
COPY .docker-hub/backend/apache-vhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY .docker-hub/backend/docker-entrypoint.sh docker-entrypoint.sh
ENTRYPOINT bash docker-entrypoint.sh
