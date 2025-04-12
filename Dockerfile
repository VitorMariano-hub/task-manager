# Etapa 1: Imagem base com PHP e Composer
FROM composer:2.6 as vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Etapa 2: Imagem PHP com extensões
FROM php:8.1-fpm

# Instalar extensões necessárias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl

# Instalar Composer globalmente (opcional se já veio da imagem anterior)
COPY --from=vendor /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copiar arquivos do projeto
COPY . .

# Copiar as dependências instaladas
COPY --from=vendor /app/vendor ./vendor

# Permissões
RUN chmod -R 775 storage bootstrap/cache

# Porta padrão
EXPOSE 9000

# Comando padrão
CMD ["php-fpm"]
