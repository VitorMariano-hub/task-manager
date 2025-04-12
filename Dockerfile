# Use uma imagem oficial do PHP como base
FROM php:8.1-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip git

# Instalar extensões do PHP necessárias para o Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Configurar o diretório de trabalho dentro do contêiner
WORKDIR /var/www

# Copiar os arquivos do projeto para dentro do contêiner
COPY . .

# Instalar dependências do Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Instalar as dependências do Laravel
RUN composer install

# Permissões do diretório de cache do Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expor a porta que o contêiner vai rodar
EXPOSE 9000

# Configurar o servidor FPM do PHP
CMD ["php-fpm"]
