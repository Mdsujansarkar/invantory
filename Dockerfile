FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    && docker-php-ext-install pdo_mysql zip bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY ./src /var/www/html

RUN composer install

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
