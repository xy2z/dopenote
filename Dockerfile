# -----------------
# NPM build
# -----------------
FROM node:8.16.1 as npm
WORKDIR /app
RUN mkdir -p /app/public/js
COPY ./package.json ./webpack.mix ./package-lock.json /app/
COPY ./resources/js/ /app/resources/js/
COPY ./resources/css/ /app/resources/css/
RUN npm i
RUN npm run production



# -----------------
# App build
# -----------------
FROM php:7.3.9-apache

LABEL maintainer "Alexander LP xy2z <xy2z@pm.me>"

# Packages
RUN apt update && apt install -y netcat

# Apache
COPY ./docker/apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# PHP Extensions
RUN apt-get update && \
    apt-get install -y --no-install-recommends git zip unzip libzip-dev
RUN docker-php-ext-configure zip --with-libzip
RUN docker-php-ext-install pdo_mysql zip
RUN docker-php-ext-enable pdo_mysql zip

# Add app
COPY . /app
WORKDIR /app

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-plugins --no-scripts

# Copy files from npm
COPY --from=npm /app/public/js /app/public/js
COPY --from=npm /app/public/css /app/public/css

# Entrypoint
# RUN chmod +x /app/docker/entrypoint.sh
ENTRYPOINT ["/app/docker/entrypoint.sh"]
CMD ["apache2-foreground"]

# Clean up to decrease image size
RUN apt-get update && \
    apt-get purge -y curl ca-certificates && \
    apt-get autoremove -y && \
    apt-get clean
