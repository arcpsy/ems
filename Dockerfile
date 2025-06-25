# Use the official PHP image with Apache
FROM php:8.2-apache

# Update package list and install dependencies for PHP extensions
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev

# Install the mysqli extension
RUN docker-php-ext-install mysqli

# Copy all your project files into the web directory
COPY . /var/www/html/

# (Optional) Enable Apache mod_rewrite if your PHP app needs it
RUN a2enmod rewrite

# Set safe permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
