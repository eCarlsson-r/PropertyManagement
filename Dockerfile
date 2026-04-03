# ---- PHP base ----
FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip git curl nodejs npm \
    && docker-php-ext-install pdo pdo_pgsql 

COPY . /var/www
WORKDIR /var/www

# Install PHP deps
RUN curl -sS https://getcomposer.org/installer | php \
    && php composer.phar install --no-dev --optimize-autoloader

# Install Node deps & build frontend
RUN npm install && npm run build

# Start server
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}