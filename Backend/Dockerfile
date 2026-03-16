FROM php:8.4-fpm

# Instalação de dependências
RUN apt-get update && apt-get install -y \
    git curl unzip libpng-dev libonig-dev libxml2-dev libpq-dev zip libssl-dev

RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring bcmath gd
RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --optimize-autoloader --no-interaction --no-scripts

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

ENV PORT=8000
EXPOSE 8000

CMD php artisan package:discover && \
    php artisan config:clear && \
    php artisan scribe:generate && \
    php artisan serve --host=0.0.0.0 --port=$PORT