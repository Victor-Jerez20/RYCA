FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php \
    && composer install --no-dev --no-interaction --prefer-dist

COPY . .

RUN php artisan config:clear || true \
    && php artisan cache:clear || true

CMD php artisan serve --host=0.0.0.0 --port=8080
