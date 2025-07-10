# Dockerfile.api
FROM php:8.3-apache

# Ensure all packages are updated to the latest security patches
RUN apt-get update && apt-get upgrade -y && apt-get clean && rm -rf /var/lib/apt/lists/*

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

# Configurando o php.ini para aumentar o tamanho máximo de upload e POST
RUN echo "upload_max_filesize = 1G" >> /usr/local/etc/php/php.ini
RUN echo "post_max_size = 1G" >> /usr/local/etc/php/php.ini
RUN echo "memory_limit = 1G" >> /usr/local/etc/php/php.ini

# Criando o diretório de sessões e configurando permissões
RUN mkdir -p /var/lib/php/sessions
RUN chown -R www-data:www-data /var/lib/php/sessions
RUN echo "session.save_path = /var/lib/php/sessions" >> /usr/local/etc/php/php.ini

RUN service apache2 restart

EXPOSE 80
