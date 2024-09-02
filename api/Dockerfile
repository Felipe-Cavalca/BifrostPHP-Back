# Dockerfile.api
FROM php:8.3-apache

COPY ./ /var/www/html

# instalando os módulos necessários
RUN a2enmod rewrite
RUN a2enmod headers
RUN docker-php-ext-install pdo_mysql

# Instalando a extensão PDO para PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql

# instalando a extensão PHP Redis
RUN pecl install redis && docker-php-ext-enable redis

RUN service apache2 restart

EXPOSE 80
