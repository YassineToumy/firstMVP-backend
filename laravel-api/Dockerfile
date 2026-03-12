FROM dunglas/frankenphp:php8.4

# Installer git pour permettre le fallback source de Composer
RUN apt-get update && apt-get install -y git && rm -rf /var/lib/apt/lists/*

# Extensions PHP
RUN install-php-extensions pdo_pgsql gd zip exif ftp opcache @composer

# Configuration PHP pour l'upload
RUN echo "upload_max_filesize = 100M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 150M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 3G" >> /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /app

# Force cache invalidation when code changes
ARG CACHEBUST=1

# Copie tout
COPY . .

# Installe les dépendances avec retry et allow superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-interaction || \
    (sleep 5 && composer install --no-dev --optimize-autoloader --no-interaction)

# Permissions Laravel
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 755 /app/storage /app/bootstrap/cache

# Copie le Caddyfile
COPY Caddyfile /etc/caddy/Caddyfile

EXPOSE 80

CMD ["sh", "-c", "php artisan config:clear && php artisan route:clear && php artisan migrate --force && frankenphp run --config /etc/caddy/Caddyfile"]
