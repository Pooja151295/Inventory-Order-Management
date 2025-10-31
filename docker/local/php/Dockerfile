# Start from a base PHP-FPM image
FROM php:8.3-fpm-alpine

# Install necessary system dependencies (git, packages) and PHP extensions
RUN apk add --no-cache \
    git \
    curl \
    libxml2-dev \
    # MySQL driver is crucial for your project
    mysql-client \
    # Install common PHP extensions
    && docker-php-ext-install pdo pdo_mysql bcmath opcache 

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www/app

# Expose PHP-FPM port
EXPOSE 9000

CMD ["php-fpm"]