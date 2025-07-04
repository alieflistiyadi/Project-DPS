FROM php:8.2-apache

# Install MySQLi extension
RUN docker-php-ext-install mysqli

# Enable Apache mod_rewrite (optional)
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Expose Apache port
EXPOSE 80
