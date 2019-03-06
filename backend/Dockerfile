FROM php:7.2.15-apache

WORKDIR /var/www/ecamp3

RUN apt-get -y update && apt-get -y upgrade && apt-get -y install libxml2-dev

RUN docker-php-ext-install pdo pdo_mysql mbstring xml

RUN a2enmod rewrite
COPY apache-vhost.conf /etc/apache2/sites-enabled/000-default.conf
