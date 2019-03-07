FROM php:7.2.15-apache

WORKDIR /var/www/ecamp3

RUN apt-get -y update && apt-get -y upgrade && apt-get -y install libxml2-dev curl unzip

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN yes | pecl install xdebug-2.7.0 && docker-php-ext-enable xdebug
ENV XDEBUG_CONFIG="remote_connect_back=1 remote_enable=1"

RUN docker-php-ext-install pdo pdo_mysql mbstring xml

RUN a2enmod rewrite
COPY apache-vhost.conf /etc/apache2/sites-enabled/000-default.conf

COPY docker-entrypoint.sh .
ENTRYPOINT bash docker-entrypoint.sh
