FROM php:8.2-fpm

# Install dependencies
RUN apt-get update \
&& apt-get -y install g++ git curl unzip libzip-dev libcurl3-dev\
&& pecl install xdebug \
&& docker-php-ext-enable xdebug

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data