# Use official PHP + Apache image
FROM php:8.2-apache

# Enable Apache rewrite module (optional but useful)
RUN a2enmod rewrite

# Install PostgreSQL extension
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copy custom Apache config
COPY apache.conf /etc/apache2/sites-enabled/000-default.conf

# Copy project files to Apache root
COPY . /var/www/html/

# Set file permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
