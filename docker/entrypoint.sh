#!/bin/bash
set -e

if [ "$1" != "apache2-foreground" ]; then
	exec "$@"
	exit
fi


echo "Preparing Dopenote..."

# Create .env if it doesnt exist
if [ ! -f /app/.env ]; then
	cp /app/docker/env /app/.env
fi

# Clear cache and generate key
php artisan key:generate
php artisan config:clear
php artisan config:cache

# Wait for database to be up
until nc -z ${DB_HOST} 3306; do sleep 1; echo "Waiting for DB to come up..."; done

# Run migrations
php artisan migrate --force

# Run php-apache entrypoint
exec /usr/local/bin/docker-php-entrypoint "$@"
