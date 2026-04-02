#!/usr/bin/env bash

# Cache configuration
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# Run migrations (only if needed/configured to run on container startup)
php artisan migrate --force

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
nginx -g "daemon off;"
