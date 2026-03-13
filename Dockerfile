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

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 755 storage bootstrap/cache

EXPOSE 80

CMD ["sh", "-c", "php artisan migrate --force; apache2-foreground"]
