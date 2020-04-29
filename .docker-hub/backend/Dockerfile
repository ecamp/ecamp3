# composer stage
FROM composer:1.8 AS composer-stage
WORKDIR /var/www/ecamp3
ENV env=prod
COPY backend/composer.* ./
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist --no-suggest
COPY backend .
RUN composer di-generate-aot

# production stage
FROM php:7.2.15-apache AS production-stage
WORKDIR /var/www/ecamp3
ENV env=prod
RUN apt-get -y update && apt-get -y upgrade && apt-get -y install libxml2-dev curl unzip iproute2
RUN docker-php-ext-install pdo pdo_mysql mbstring xml
COPY --from=composer-stage /var/www/ecamp3/vendor /var/www/ecamp3/vendor
COPY backend .
RUN chmod -R a+rw data
RUN a2enmod rewrite
COPY backend/apache-vhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY .docker-hub/backend/docker-entrypoint.sh docker-entrypoint.sh
ENTRYPOINT bash docker-entrypoint.sh
