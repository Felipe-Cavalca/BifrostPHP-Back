# Documentação da API BifrostPHP

Esta pasta contém informações de referência para o módulo da API. As seções abaixo fornecem uma visão geral do projeto, como executar o serviço e como gerar a documentação para desenvolvedores.

## Visão geral

BifrostPHP é um framework minimalista voltado para a construção de pequenas APIs usando Docker como ambiente padrão de desenvolvimento. Este repositório representa o módulo de back-end escrito em PHP.

## Estrutura de pastas

```
api/
├── Attributes    # Atributos personalizados em PHP
├── Class         # Classes do sistema
├── Controller    # Controladores de endpoints
├── Core          # Tratamento de requisições e utilidades
├── DataTypes     # Objetos de valor fortemente tipados
├── Docs          # Esta documentação
├── Enum          # Definições de enums
├── Include       # traits para reutilização de código
├── Interface     # Contratos de interface
├── Model         # Modelos de banco de dados
└── tests         # Requisições de exemplo para testes funcionais
```

## Executando a API localmente

1. Instale o [Docker](https://docs.docker.com/get-docker/) e o [Docker Compose](https://docs.docker.com/compose/install/).

2. Crie um arquivo `.env` se baseando no `.env.example`

3. Navegue até o diretório `api`.
   ```bash
   cd api
   ```

4. Inicie os contêineres:
   ```bash
   docker-compose up -d
   ```

5. Acesse a API em [http://localhost:80](http://localhost:80).
