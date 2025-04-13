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

# Copia o script de entrada
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Instala as dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Permissões para diretórios necessários
RUN chmod -R 775 storage bootstrap/cache

# Expõe a porta
EXPOSE 8080

# Define o script de entrada como comando padrão
CMD ["entrypoint.sh"]
