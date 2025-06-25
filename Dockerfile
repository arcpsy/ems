# Use the official PHP image with Apache
FROM php:8.2-apache

# Copy all your project files into the web directory
COPY . /var/www/html/

# (Optional) Enable Apache mod_rewrite if your PHP app needs it
RUN a2enmod rewrite

# Set safe permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
