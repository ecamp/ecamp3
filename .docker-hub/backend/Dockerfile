# composer stage
FROM composer:2@sha256:4fb6902b816f7c5c8324c57117749047cb50aff738ca34d5c6e9159df56bb27b AS composer-stage
WORKDIR /app
ENV env=prod
COPY backend/composer.* ./
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist
COPY backend .
RUN composer di-generate-aot

# production stage
FROM php:7.4.20-apache@sha256:d136e484be0e378e1342f174c7d95903138fadad6e8acdb593eee7163d6df3ee AS production-stage
WORKDIR /app
ENV env=prod
RUN apt-get -y update && apt-get -y upgrade && apt-get -y install libxml2-dev curl unzip iproute2 libonig-dev && docker-php-ext-install pdo pdo_mysql xml
COPY --from=composer-stage /app/vendor /app/vendor
COPY backend .
RUN chmod -R a+rw data && a2enmod rewrite
COPY .docker-hub/backend/apache-vhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY .docker-hub/backend/docker-entrypoint.sh docker-entrypoint.sh
ENTRYPOINT bash docker-entrypoint.sh
