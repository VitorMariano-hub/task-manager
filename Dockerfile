FROM php:8.3-fpm

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
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl pgsql pdo_pgsql

# Instala o Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Define diretório de trabalho
WORKDIR /var/www

# Copia todo o projeto para dentro do container
COPY . .

# Instala as dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Rodar as migrações do Laravel
RUN php artisan migrate --force

# Permissões para diretórios necessários
RUN chmod -R 775 storage bootstrap/cache

# Expõe a porta padrão
EXPOSE 8080

# Comando padrão
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
