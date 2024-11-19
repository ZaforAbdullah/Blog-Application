# Use an official PHP image with Apache as the base image.
FROM php:8.1-apache

# Set environment variables.
ENV ACCEPT_EULA=Y
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install system dependencies.
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions.
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql exif \
    && pecl install xdebug-3.3.1 \
    && docker-php-ext-enable xdebug

# Enable Apache modules required for Laravel.
RUN a2enmod rewrite

# Set the Apache document root
ENV APACHE_DOCUMENT_ROOT /var/www/html/

# Update the default Apache site configuration
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Create a directory for your Laravel application.
WORKDIR /var/www/html/

# Copy the Laravel application files into the container.
COPY . .

# Install Composer globally.
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set permissions for Laravel.
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port 80 for Apache.
EXPOSE 80

# Start Apache web server.
CMD ["apache2-foreground"]

