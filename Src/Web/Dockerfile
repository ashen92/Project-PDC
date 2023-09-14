FROM php:8.1-apache
RUN apt-get update && apt-get install -y

RUN docker-php-ext-install pdo pdo_mysql

RUN pecl install apcu \
    && docker-php-ext-enable apcu

RUN a2enmod rewrite

COPY ./Configs/Docker/GroupProject.conf /etc/apache2/sites-available/GroupProject.conf
RUN a2dissite 000-default.conf
RUN a2ensite GroupProject
COPY ./Configs/Docker/php.ini /etc/php/8.1/apache2/php.ini

COPY ./App/ /var/www/
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer
WORKDIR /var/www/
RUN composer install --no-dev