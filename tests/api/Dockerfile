FROM php:8.3-cli

# Copiar PHPUnit PHAR
COPY ./tests/api/phpunit/phpunit-12.3.4.phar /usr/local/bin/phpunit
RUN chmod +x /usr/local/bin/phpunit

# Definir diretório de trabalho
WORKDIR /app

# Copiar todo o código da API (necessário para os testes)
COPY ./ ./

# Comando padrão para rodar os testes
CMD ["phpunit", "--configuration", "tests/api/phpunit.xml"]