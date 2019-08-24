FROM php:7.2-apache

# Apache
RUN a2enmod rewrite

# PHP Extensions
RUN docker-php-ext-install mysqli pdo_mysql
RUN docker-php-ext-enable mysqli pdo_mysql

# Composer
RUN apt-get update && \
    apt-get install -y git zip unzip
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer --version
