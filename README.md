# BifrostPHP – Módulo de Serviço Back-End

BifrostPHP é um micro-framework escrito em PHP 8+ que prioriza produtividade e código limpo através do uso massivo de _Attributes_.
Este repositório concentra o **núcleo de serviços HTTP** do framework – roteador, camadas de requisição e resposta, cache, fila de tarefas, validação, entre outros.

<div align="center">
  <img src="https://img.shields.io/badge/PHP-%3E=8.1-blue" alt="PHP 8.1+" />
  <img src="https://img.shields.io/badge/license-MIT-green" alt="MIT License" />
</div>

---

## Principais recursos

* 🚦 **Roteamento automático** baseado nos parâmetros `_controller` e `_action` ou em enums de rotas.
* 🧩 **Attributes** de primeira classe (Cache, Method, RequiredParams, Details, etc.).
* ⚡ **HTTP Responses** tipadas via `Bifrost\Class\HttpResponse` com _helpers_ para _status codes_ usuais.
* 🗄️ **Cache** e **Fila de Tarefas** prontos para uso através do Redis.
* 🔐 **Validação** de parâmetros GET/POST por meio do enum `Field` (Email, UUID, CPF, …).
* ♻️ Pipeline `beforeRun` / `afterRun` para reutilizar lógica transversal.
* 🐳 Ambiente **Docker** e **Nginx** configurado para _hot-reload_ e _load-balancing_ (2 workers PHP por padrão).

## Índice

1. [Começando](#começando)
2. [Estrutura de pastas](#estrutura-de-pastas)
3. [Atributos disponíveis](#atributos-disponíveis)
4. [Variáveis de ambiente](#variáveis-de-ambiente)
5. [Exemplos de uso](#exemplos-de-uso)
6. [Testes](#testes)
7. [Contribuição](#contribuição)
8. [Licença](#licença)

---

## Começando

### Pré-requisitos

* **Docker** e **Docker Compose** ― recomendados.
* ou **PHP 8.1+**, Composer, e as extensões: `redis`, `pdo`, `pdo_mysql` / `sqlite`, etc.

### Subindo com Docker

```bash
# Clonar o projeto
$ git clone https://github.com/SeuUsuario/BifrostPHP-Back.git
$ cd BifrostPHP-Back

# Copie as variáveis de ambiente
$ cp .env.example .env   # Ajuste as variáveis se necessário

# Suba a stack
$ docker compose up -d --build
```

O Nginx ficará disponível em `http://localhost/api/` expondo dois workers PHP (`api1`, `api2`).

### Acesso rápido

Requisição de exemplo para o controller padrão:

```bash
curl "http://localhost/index/index?id=123e4567-e89b-12d3-a456-426614174000"
```

Resposta esperada (código 200):

```json
{
  "status": 200,
  "message": "Recurso",
  "data": {
    "id": "123e4567-e89b-12d3-a456-426614174000"
  },
  "errors": null
}
```

---

## Estrutura de pastas

```
api/
│   docker-compose.yml   # Orquestração dos containers
│   Dockerfile*          # Imagens de produção e dev
│
├── Attributes/          # Implementações dos PHP Attributes
├── Class/               # Classes auxiliares (HttpResponse, …)
├── Controller/          # Controllers da aplicação
├── Core/                # Kernel (Request, Settings, Cache, Queue, …)
├── DataTypes/           # Tipos auxiliares (base64, url, …)
├── Enum/                # Enums (Field, HttpStatusCode, Routes, …)
├── Include/             # Traits e helpers compartilhados
├── Interface/           # Contratos (ControllerInterface, TaskInterface, …)
├── Model/               # Repositórios / modelos de domínio
└── Tasks/               # Tarefas assíncronas executadas na Queue
```

---

## Atributos disponíveis

| Attribute | Descrição |
|-----------|-----------|
| `#[Method("GET")]` | Restringe o endpoint ao(s) método(s) HTTP informados. |
| `#[Cache(60)]` | Armazena a resposta por _N_ segundos no Redis, levando em conta GET, POST e sessão. |
| `#[RequiredParams(["id" => Field::UUID])]` | Exige e valida parâmetros GET/POST antes de executar a ação. |
| `#[OptionalParams([...])]` | Validação opcional de parâmetros. |
| `#[OptionalFields([...])]` / `#[RequiredFields([...])]` | Validação de campos em corpo de requisição (para métodos não-GET). |
| `#[Details([...])]` | Metadados usados para _self-documentation_. |
| `#[Response(status: 201, description: "Created")]` | Documenta o objeto resposta esperado. |

Cada atributo pode expor opções em tempo de execução, recuperáveis via:

```php
Request::getOptionsAttributes($this, "get_recurso");
```

---

## Variáveis de ambiente

| Nome | Descrição | Default |
|------|-----------|---------|
| `BFR_API_DISPLAY_ERRORS` | Exibir _errors_ do PHP | `false` |
| `BFR_API_REDIS_HOST` | Host do Redis para **Cache** e **Queue** | `redis` |
| `BFR_API_REDIS_PORT` | Porta do Redis | `6379` |
| `BFR_API_SESSION_SAVE_HANDLER` | Driver de sessão PHP | `files` |
| `BFR_API_SESSION_SAVE_PATH` | Path para salvar sessão | `/var/lib/php/sessions` |
| `BFR_API_SESSION_GC_MAXLIFETIME` | TTL da sessão (segundos) | `1440` |
| `BFR_API_SESSION_COOKIE_LIFETIME` | TTL do cookie de sessão | `0` |

> Consulte `api/Core/Settings.php` para a lista completa de chaves lidas pelo sistema.

---

## Exemplos de uso

### Criando um novo Controller

```php
<?php
namespace App\Controller;

use Bifrost\Attributes\Method;
use Bifrost\Class\HttpResponse;
use Bifrost\Interface\Controller;

class User implements Controller
{
    #[Method("GET")]
    public function list()
    {
        return HttpResponse::success("Lista de usuários", [
            // ...
        ]);
    }
}
```

Acesso:

```
GET /api/User/list
```

### Agendando tarefas na fila

```php
use Bifrost\Core\Queue;
use App\Tasks\SendEmail;

$queue = new Queue();
$task  = new SendEmail($userId);
$queue->addScheduledTask($task, 60); // Executar em 1 minuto
```

---

## Testes

Os testes ainda não foram adicionados ao repositório. Contribuições com PHPUnit são bem-vindas!

---

## Contribuição

1. _Fork_ este repositório
2. Crie uma _branch_ para sua feature: `git checkout -b feature/minha-feature`
3. Commit: `git commit -m 'Minha nova feature'`
4. Push: `git push origin feature/minha-feature`
5. Abra um _Pull Request_

Por favor, mantenha um estilo de código consistente e escreva testes sempre que possível.

---

## Licença

Distribuído sob a licença MIT. Consulte o arquivo [LICENSE](LICENSE) para obter mais informações.

> Documentação desenvolvida com auxilio de I.A.