# =============================================================================
# Stage 1: Build frontend assets (Vite + Tailwind CSS)
# =============================================================================
FROM node:18-alpine AS frontend

WORKDIR /build

# Copy only package files first to leverage Docker layer caching.
COPY package.json package-lock.json ./
RUN npm ci

# Copy frontend source files and build.
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources/ resources/
RUN npm run build

# =============================================================================
# Stage 2: PHP application
# =============================================================================
FROM php:8.3-fpm

# Build args let you map host UID/GID to avoid permission issues on bind mounts.
ARG UID=1000
ARG GID=1000

# Install system dependencies and PHP extensions required by Laravel.
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libpq-dev \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install latest Composer from the official Composer image.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create an app user/group that matches host UID/GID for safer file permissions.
RUN groupadd -g ${GID} laravel \
    && useradd -u ${UID} -g laravel -m -s /bin/bash laravel \
    && sed -ri 's/^user = www-data/user = laravel/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -ri 's/^group = www-data/group = laravel/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -ri 's/^listen.owner = www-data/listen.owner = laravel/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -ri 's/^listen.group = www-data/listen.group = laravel/' /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www

# Copy composer files first to leverage Docker layer caching for dependencies.
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-scripts --no-interaction

# Copy the rest of the application source code.
COPY . .

# Run post-install scripts now that full source is available.
RUN composer run-script post-autoload-dump

# Copy built frontend assets from Stage 1.
COPY --from=frontend /build/public/build public/build

# Ensure runtime directories exist and have correct ownership.
RUN mkdir -p storage/framework/{sessions,views,cache} bootstrap/cache \
    && chown -R laravel:laravel storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# App startup script: fix permissions and run migrations before PHP-FPM starts.
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
