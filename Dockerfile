FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

RUN apk update && apk add \
    build-base \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    nginx \
    supervisor

RUN docker-php-ext-install pdo_mysql zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . .

RUN chown -R www-data:www-data /var/www/html

CMD ["php-fpm"]