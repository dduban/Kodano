FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    && docker-php-ext-install \
    pdo_mysql \
    zip \
    intl \
    opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
EXPOSE 9000
CMD ["php-fpm"]
