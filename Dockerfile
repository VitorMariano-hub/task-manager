# Etapa 1: imagem base com PHP e extensões necessárias
FROM php:8.1-fpm

# Instala pacotes essenciais
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl

# Instala o Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Define diretório de trabalho
WORKDIR /var/www

# Copia todo o projeto para dentro do container
COPY . .

# Instala as dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Permissões para diretórios necessários
RUN chmod -R 775 storage bootstrap/cache

# Expõe a porta padrão
EXPOSE 9000

# Comando padrão
CMD ["php-fpm"]
