#####
##### Build Stage
#####

FROM node:18.18-slim AS build

COPY . .
WORKDIR /App

RUN npm install -g gulp-cli
RUN npm install

RUN gulp --gulpfile gulpfile.prod.js

#####
##### Production Stage
#####

FROM php:8.1-apache
RUN apt-get update && apt-get install -y
RUN apt-get install git libzip-dev dos2unix -y

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN docker-php-ext-install zip pdo pdo_mysql

RUN pecl install apcu \
    && docker-php-ext-enable apcu

RUN a2enmod rewrite
RUN a2enmod headers

COPY ./.docker/apache/App.conf /etc/apache2/sites-available/App.conf
COPY ./.docker/apache/apache2.conf /etc/apache2/apache2.conf
COPY ./.docker/apache/mime.conf /etc/apache2/mods-enabled/mime.conf
RUN a2dissite 000-default.conf
RUN a2ensite App

COPY ./App/ /var/www/
COPY ./.docker/config/doctrine-config.php /var/www/src/doctrine-config.php
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

WORKDIR /var/www/

COPY --from=build /App/public/js/ ./public/js/
COPY --from=build /App/public/css/ ./public/css/

RUN composer install --no-dev --optimize-autoloader

#####
##### Configure SSH for Azure Web Service
#####

COPY ./.docker/entrypoint.sh ./
RUN dos2unix ./entrypoint.sh
# Start and enable SSH
RUN apt-get install -y --no-install-recommends dialog
RUN apt-get install -y --no-install-recommends openssh-server
RUN echo "root:Docker!" | chpasswd \
    && chmod u+x ./entrypoint.sh
COPY ./.docker/sshd_config /etc/ssh/
EXPOSE 8000 2222
ENTRYPOINT [ "./entrypoint.sh" ]
