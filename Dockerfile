# === Stage 1: Build frontend assets (Vite) ===
FROM node:20-alpine AS assets-builder
WORKDIR /app

# Copy configuration files
COPY package*.json ./
COPY vite.config.js ./
COPY postcss.config.js ./
COPY tailwind.config.js ./

# Copy resources and public assets
COPY resources/ ./resources/
COPY public/ ./public/

# Install dependencies and build assets
RUN npm ci
RUN npm run build

# === Stage 2: Main Application Runtime ===
FROM php:8.3-fpm-alpine
WORKDIR /var/www

# Install production system dependencies
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    libzip-dev \
    postgresql-dev \
    bash

# Configure GD extension
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    zip \
    gd \
    bcmath \
    opcache \
    exif \
    pcntl

# Configure OPcache for production performance
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=10000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application source
COPY . /var/www

# Copy compiled assets from frontend-builder stage
COPY --from=assets-builder /app/public/build /var/www/public/build

# Exclude dev dependencies and optimize autoload mapping
RUN composer install --no-interaction --no-dev --optimize-autoloader --prefer-dist

# Create necessary directories and set correct ownership & permissions for storage and cache
RUN mkdir -p /var/www/storage/framework/cache/data \
             /var/www/storage/framework/sessions \
             /var/www/storage/framework/views \
             /var/www/storage/logs \
             /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
