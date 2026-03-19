FROM php:8.4-apache

RUN a2enmod rewrite headers

RUN apt-get update && apt-get install -y \
    git libpq-dev libzip-dev libpng-dev libexif-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql zip gd exif opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN echo "upload_max_filesize = 100M" > /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size = 150M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html

RUN { \
    echo '<VirtualHost *:80>'; \
    echo '    DocumentRoot /var/www/html/public'; \
    echo '    <Directory /var/www/html/public>'; \
    echo '        AllowOverride All'; \
    echo '        Options -Indexes +FollowSymLinks'; \
    echo '        Require all granted'; \
    echo '    </Directory>'; \
    echo '    ErrorLog ${APACHE_LOG_DIR}/error.log'; \
    echo '    CustomLog ${APACHE_LOG_DIR}/access.log combined'; \
    echo '</VirtualHost>'; \
} > /etc/apache2/sites-available/000-default.conf

ARG CACHEBUST=2
COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 755 storage bootstrap/cache

EXPOSE 80

CMD ["sh", "-c", "php artisan config:clear && php artisan route:clear && php artisan migrate --force; apache2-foreground"]
