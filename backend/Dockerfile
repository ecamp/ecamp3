FROM composer:1.8.4 AS composer-install

COPY composer.json composer.lock ./
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist



FROM php:7.2.15-apache

RUN apt-get -y update && apt-get -y upgrade && apt-get -y install libxml2-dev

RUN groupadd -r ecamp3 && useradd --no-log-init -r -g ecamp3 ecamp3 && mkdir -p /home/ecamp3/.composer/cache/files && chown -R ecamp3 /home/ecamp3 && mkdir -p /var/www/ecamp3/vendor && chown -R ecamp3 /var/www/ecamp3/vendor
COPY --from=composer-install /app/vendor /var/www/ecamp3/vendor

RUN docker-php-ext-install pdo pdo_mysql mbstring xml

RUN a2enmod rewrite
COPY apache-vhost.conf /etc/apache2/sites-enabled/000-default.conf

WORKDIR /var/www/ecamp3
COPY . .
