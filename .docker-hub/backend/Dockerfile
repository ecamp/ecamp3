# composer stage
FROM composer:2@sha256:0266775ed3851db65389b84cdd21b54734fd4053dfd7e0cd664cd24f62caf7d5 AS composer-stage
WORKDIR /app
ENV env=prod
COPY backend/composer.* ./
RUN composer install --no-interaction --no-plugins --no-scripts --prefer-dist
COPY backend .
RUN composer di-generate-aot

# production stage
FROM php:7.4.16-apache@sha256:d846e9aa895c0c0110ada7b7353af9cd0afeaec30b0971696380495cff881f2b AS production-stage
WORKDIR /app
ENV env=prod
RUN apt-get -y update && apt-get -y upgrade && apt-get -y install libxml2-dev curl unzip iproute2 libonig-dev && docker-php-ext-install pdo pdo_mysql xml
COPY --from=composer-stage /app/vendor /app/vendor
COPY backend .
RUN chmod -R a+rw data && a2enmod rewrite
COPY .docker-hub/backend/apache-vhost.conf /etc/apache2/sites-enabled/000-default.conf
COPY .docker-hub/backend/docker-entrypoint.sh docker-entrypoint.sh
ENTRYPOINT bash docker-entrypoint.sh
