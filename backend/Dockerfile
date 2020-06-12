FROM php:7.4.2-apache

WORKDIR /app

RUN apt-get -y update && apt-get -y upgrade && apt-get -y install libxml2-dev curl unzip iproute2 libonig-dev

RUN yes | pecl install xdebug-2.9.5 && docker-php-ext-enable xdebug && touch /var/log/xdebug.log && chmod a+rw /var/log/xdebug.log
ENV XDEBUG_CONFIG="remote_enable=1 remote_autostart=1 remote_connect_back=0 remote_host=host.docker.internal remote_port=9000 remote_log=/var/log/xdebug.log"

RUN docker-php-ext-install pdo pdo_mysql xml

RUN a2enmod rewrite
COPY apache-vhost.conf /etc/apache2/sites-enabled/000-default.conf

ENTRYPOINT bash docker-entrypoint.sh
