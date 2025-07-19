# Dockerfile.api
FROM php:8.3-apache

# Atualizando pacotes do sistema
RUN apt-get update && apt-get upgrade -y && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copiando os arquivos da aplicação
COPY ./ /var/www/html

# Ativando módulos do Apache
RUN a2enmod rewrite
RUN a2enmod headers

# Instalando extensões PHP necessárias
RUN docker-php-ext-install pdo_mysql

# Instalando a extensão PDO para PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && apt-get install -y supervisor

# Instalando a extensão PHP Redis
RUN pecl install redis && docker-php-ext-enable redis

# Configurando o php.ini
RUN echo "upload_max_filesize = 1G" >> /usr/local/etc/php/php.ini
RUN echo "post_max_size = 1G" >> /usr/local/etc/php/php.ini
RUN echo "memory_limit = 1G" >> /usr/local/etc/php/php.ini
RUN echo "session.save_path = /var/lib/php/sessions" >> /usr/local/etc/php/php.ini

# Criando o diretório de sessões e configurando permissões
RUN mkdir -p /var/lib/php/sessions
RUN chown -R www-data:www-data /var/lib/php/sessions

# Criando diretórios para o supervisor
RUN mkdir -p /var/run/supervisor
RUN mkdir -p /var/log/supervisor

# Copia a configuração do supervisord
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expõe a porta HTTP
EXPOSE 80

# Inicia supervisor para rodar Apache + worker
CMD ["/usr/bin/supervisord"]
