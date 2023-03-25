# Use the official PHP image as the base image
FROM php:8.2-apache

# Install any necessary dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    && docker-php-ext-install zip

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy the project files into the container
COPY . /var/www/html

# Expose port 80 for Apache
EXPOSE 80

# Start the Apache server
CMD ["apache2-foreground"]