FROM php:8.2-apache

# Install PDO and PDO MySQL extensions for PHP (also needed in frontend)
RUN docker-php-ext-install pdo pdo_mysql

# Copy all project files into Apache's root directory
COPY . /var/www/html/

# Set file permissions for Apache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

