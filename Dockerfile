FROM php:8.3-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    zip \
    libxml2-dev \
    libonig-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo pdo_mysql dom mbstring \
    && apt-get clean

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy source code (to install composer packages)
COPY ./www /var/www/html

# Install PHPUnit & Mockery as dev dependencies
RUN composer require --dev phpunit/phpunit mockery/mockery

# Create storage directory and set permissions
RUN mkdir -p /var/www/html/public/storage \
    && chown -R www-data:www-data /var/www/html/public/storage \
    && chmod -R 775 /var/www/html/public/storage
