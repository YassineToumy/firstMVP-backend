FROM php:8.4-fpm-alpine

# Install system deps + PHP extensions
RUN apk add --no-cache git curl zip unzip libpq-dev libzip-dev nginx \
    && docker-php-ext-install pdo pdo_pgsql zip bcmath opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install dependencies first (cached layer)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy app
COPY . .

# Finish composer
RUN composer dump-autoload --optimize \
    && php artisan config:clear

# Create storage directories
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && chmod -R 775 storage bootstrap/cache

# Nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 8000

CMD php artisan migrate --force && php-fpm -D && nginx -g "daemon off;"
