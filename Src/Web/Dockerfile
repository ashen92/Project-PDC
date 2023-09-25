FROM php:apache
RUN apt-get update && apt-get install -y

RUN docker-php-ext-install pdo pdo_mysql

RUN pecl install apcu \
    && docker-php-ext-enable apcu

RUN a2enmod rewrite

COPY ./Configs/Docker/GroupProject.conf /etc/apache2/sites-available/GroupProject.conf
COPY ./Configs/Docker/apache2.conf /etc/apache2/apache2.conf
RUN a2dissite 000-default.conf
RUN a2ensite GroupProject

COPY ./App/ /var/www/
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer
WORKDIR /var/www/
RUN composer install --no-dev