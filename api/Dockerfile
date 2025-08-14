# Dockerfile.api
FROM php:8.3-fpm

# Atualizando pacotes do sistema e instalando dependências necessárias
RUN apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends \
        supervisor \
        libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copiando os arquivos da aplicação
COPY ./ /var/www/html

# Configurações do PHP (inclui otimizações de desempenho)
RUN mkdir -p /usr/local/etc/php/conf.d \
    && echo "upload_max_filesize = 1G" > /usr/local/etc/php/conf.d/bifrost.ini \
    && echo "post_max_size = 1G" >> /usr/local/etc/php/conf.d/bifrost.ini \
    && echo "memory_limit = 1G" >> /usr/local/etc/php/conf.d/bifrost.ini \
    && echo "session.save_path = /var/lib/php/sessions" >> /usr/local/etc/php/conf.d/bifrost.ini \
    && echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/bifrost.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/bifrost.ini \
    && echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/bifrost.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/bifrost.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/bifrost.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/bifrost.ini

# Criando o diretório de sessões e configurando permissões
RUN mkdir -p /var/lib/php/sessions \
    && chown -R www-data:www-data /var/lib/php/sessions

# Copiando a configuração do Supervisor
RUN mkdir -p /var/run/supervisor /var/log/supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expõe a porta do PHP-FPM
EXPOSE 9000

# Inicia supervisor para rodar PHP-FPM e o worker
CMD ["/usr/bin/supervisord"]
