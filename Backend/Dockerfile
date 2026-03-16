FROM php:8.4-fpm

# Instalação de dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    libssl-dev

# Instalação de extensões PHP
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    bcmath \
    gd

# Instalação do Redis
RUN pecl install redis && docker-php-ext-enable redis

# Copia o Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copia os arquivos do projeto
COPY . .

# Instala dependências do PHP (ignora scripts para evitar erros de banco no build)
RUN composer install --optimize-autoloader --no-interaction --no-scripts

# Ajusta permissões antes da execução
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

ENV PORT=8000
EXPOSE 8000

# O CMD agora limpa caches, descobre pacotes e gera o Scribe antes de subir o servidor
CMD php artisan package:discover && \
    php artisan config:clear && \
    php artisan scribe:generate && \
    php artisan serve --host=0.0.0.0 --port=$PORT