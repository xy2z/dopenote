FROM php:7.3.9-apache

LABEL maintainer "Alexander LP xy2z <xy2z@pm.me>"

# Packages
RUN apt update && apt install -y netcat

# Apache
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# PHP Extensions
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-enable pdo_mysql

# Add app
VOLUME /app
COPY . /app
WORKDIR /app

# Composer
RUN apt-get update && \
    apt-get install -y --no-install-recommends git zip unzip
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-plugins --no-scripts

# Entrypoint
RUN chmod +x /app/docker/entrypoint.sh
ENTRYPOINT ["/app/docker/entrypoint.sh"]
CMD ["apache2-foreground"]

# Clean up to decrease image size
RUN apt-get update && \
    apt-get purge -y curl ca-certificates && \
    apt-get autoremove -y && \
    apt-get clean && \
    rm /app/composer.json /app/composer.lock
