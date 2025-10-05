# Use official PHP image with Apache
FROM php:8.2-apache

WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y libzip-dev zip unzip git curl && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Laravel files
COPY . /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Set Apache DocumentRoot to Laravel public folder
RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf

# Install Laravel dependencies
RUN composer install --optimize-autoloader --no-dev

# Create storage symlink
RUN php artisan storage:link || true

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
