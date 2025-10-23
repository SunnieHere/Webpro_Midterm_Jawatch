# --- Laravel Production Dockerfile ---
FROM php:8.3-apache

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Link storage for public file access
RUN php artisan storage:link || true

# Fix permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose web port
EXPOSE 8000

# Start app
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000
