FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libcurl4-openssl-dev \
    unzip \
    zip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        curl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Instalar dependencias
RUN composer install --optimize-autoloader

# Cachear configuraciones (importante para producción)
RUN php artisan config:cache
RUN php artisan route:cache

# Crear directorios
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 10000

CMD sh -c "php artisan storage:link || true && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=10000"