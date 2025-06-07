# Documentação da API BifrostPHP

Esta pasta contém informações de referência para o módulo da API. As seções abaixo fornecem uma visão geral do projeto, como executar o serviço e como gerar a documentação para desenvolvedores.

## Visão geral

BifrostPHP é um framework minimalista voltado para a construção de pequenas APIs usando Docker como ambiente padrão de desenvolvimento. Este repositório representa o módulo de back-end escrito em PHP.

## Estrutura de pastas

```
api/
├── Attributes    # Atributos personalizados em PHP
├── Class         # Classes principais usadas pelo framework
├── Controller    # Controladores de endpoints
├── Core          # Tratamento de requisições e utilidades
├── Model         # Modelos de banco de dados
├── DataTypes     # Objetos de valor fortemente tipados
├── Enum          # Definições de enums
├── Interface     # Contratos de interface
├── tests         # Requisições de exemplo para testes funcionais
└── docs          # Esta documentação
```

## Executando a API localmente

1. Instale o [Docker](https://docs.docker.com/get-docker/) e o [Docker Compose](https://docs.docker.com/compose/install/).
2. Navegue até o diretório `api`.
3. Inicie os contêineres:
   ```bash
   docker-compose up -d
   ```
4. Acesse a API em [http://localhost:80](http://localhost:80).
