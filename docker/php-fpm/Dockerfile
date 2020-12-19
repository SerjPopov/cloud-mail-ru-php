FROM php:7.0-fpm

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update \
&& pecl install xdebug-2.6.1 \
&& docker-php-ext-enable xdebug
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data