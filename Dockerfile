# =========================
# Etapa 1: Compilar React
# =========================
FROM node:20-alpine AS frontend-build

WORKDIR /frontend

COPY Frontend/package*.json ./

RUN npm install

COPY Frontend/ .

RUN npm run build


# =========================
# Etapa 2: Laravel
# =========================
FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libpng-dev \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY Backend/ .

RUN composer install --no-dev --optimize-autoloader

# Copiamos React compilado
COPY --from=frontend-build /frontend/dist ./public

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 10000

CMD php artisan migrate --force && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-10000}