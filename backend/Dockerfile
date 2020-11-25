FROM php:7.4.13-apache

WORKDIR /app

RUN apt update
RUN apt install -y iproute2

# Copy development php.ini
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Apache site config
RUN a2enmod rewrite
COPY apache-vhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY apache-ports.conf /etc/apache2/ports.conf

# MySQL driver
RUN docker-php-ext-install pdo pdo_mysql

# XDebug
RUN pecl install xdebug-2.9.6
RUN docker-php-ext-enable xdebug
RUN touch /var/log/xdebug.log && chmod a+rw /var/log/xdebug.log
ENV XDEBUG_CONFIG="remote_enable=1 remote_autostart=1 remote_connect_back=1 remote_host=docker-host remote_port=9000 remote_log=/var/log/xdebug.log"

# Install composer for PHP dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Make log directory writable for arbitrary users
RUN chmod -R 777 /var/log/apache2
