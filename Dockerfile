FROM dunglas/frankenphp:php8.4

RUN install-php-extensions pdo_pgsql zip gd exif opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN echo "upload_max_filesize = 100M" > /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size = 150M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/custom.ini

WORKDIR /app

COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 755 storage bootstrap/cache

EXPOSE 80

CMD ["sh", "-c", "php artisan config:clear && php artisan route:clear && php artisan migrate --force && frankenphp run --config /etc/caddy/Caddyfile"]
