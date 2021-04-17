FROM php:7.4.16-apache@sha256:a6d15a7d41b5bdeb248dc54ecbcceba9ae1daec391ecbec71a2f8ed2893d90b3

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
RUN pecl install xdebug-3.0.2
RUN docker-php-ext-enable xdebug
RUN touch /var/log/xdebug.log && chmod a+rw /var/log/xdebug.log
ENV XDEBUG_MODE="develop,debug,coverage"
ENV XDEBUG_CONFIG="client_host=docker-host log=/var/log/xdebug.log"

# Install composer for PHP dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Make log directory writable for arbitrary users
RUN chmod -R 777 /var/log/apache2
