<?php

namespace Bifrost\Class;

use Bifrost\Enum\HttpStatusCode;

/**
 * Classe para tratar respostas HTTP.
 */
class HttpResponse
{

    public function __construct(
        private HttpStatusCode $status = HttpStatusCode::INTERNAL_SERVER_ERROR,
        private ?string $message = null,
        private null|array $data = null,
        private ?array $errors = null,
        private array $additionalInfo = []
    ) {}

    public function __toString(): string
    {
        http_response_code($this->status->value);
        return json_encode(
            array_merge([
                "status" => $this->status->value,
                "message" => $this->message ?? $this->status->message(),
                "data" => $this->data,
                "errors" => $this->errors,
            ], $this->additionalInfo),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Adiciona informações extras à resposta.
     * @param array $additionalInfo - Informações adicionais a serem adicionadas.
     * @return void
     */
    public function addAditionalInfo(array $additionalInfo): void
    {
        $this->additionalInfo = array_merge($this->additionalInfo, $additionalInfo);
    }


    // Métodos estáticos para construir respostas comuns


    /**
     * Constrói uma resposta de sucesso.
     * @param string $message Mensagem a ser retornada.
     * @param array $data Dados adicionais sobre o endpoint.
     * @return self Instância da classe HttpResponse com status 200 (OK).
     */
    public static function success(?string $message = null, ?array $data = null): self
    {
        return new self(
            status: HttpStatusCode::OK,
            message: $message,
            data: $data
        );
    }

    /**
     * Constrói uma resposta para criação de recursos.
     * @param string $objName Nome do objeto criado.
     * @param array $data Dados sobre o recurso criado.
     * @return self Instância da classe HttpResponse com status 201 (Created).
     */
    public static function created(string $objName, array $data): self
    {
        return new self(
            status: HttpStatusCode::CREATED,
            message: $objName . " created successfully",
            data: $data
        );
    }

    /**
     * Retorna as opções de métodos permitidos para o cliente.
     * @param array $methods Métodos permitidos.
     * @return self Resposta de opções de métodos permitidos para o cliente.
     */
    public static function options(array $methods): self
    {
        header("Access-Control-Allow-Methods: " . implode(", ", $methods));
        return new self(
            status: HttpStatusCode::OK,
            message: "Allow Methods",
            additionalInfo: ["methods" => $methods]
        );
    }

    /**
     * Retorna a resposta de não encontrado para o cliente
     * @param array $errors Dados adicionais sobre o erro.
     * @param string $message Mensagem a ser retornada.
     * @return self Resposta de não encontrado para o cliente.
     */
    public static function notFound(array $errors, ?string $message = null): self
    {
        return new self(
            status: HttpStatusCode::NOT_FOUND,
            message: $message,
            errors: $errors
        );
    }

    /**
     * Retorna a resposta de método não permitido para o cliente
     * @param string $message Mensagem a ser retornada.
     * @return self Resposta de método não permitido para o cliente.
     */
    public static function methodNotAllowed(string $message): self
    {
        return new self(
            status: HttpStatusCode::METHOD_NOT_ALLOWED,
            message: $message,
            errors: [
                "method" => $_SERVER["REQUEST_METHOD"]
            ]
        );
    }

    /**
     * Retorna a resposta de erro de requisição para o cliente
     * @param string $message Mensagem de erro a ser retornada.
     * @param array $errors Dados adicionais sobre o erro.
     * @return self Resposta de erro de requisição para o cliente.
     */
    public static function badRequest(array $errors, ?string $message = null): self
    {
        return new self(
            status: HttpStatusCode::BAD_REQUEST,
            message: $message,
            errors: $errors
        );
    }

    /**
     * Retorna a resposta de conflito para o cliente
     * @param string $message Mensagem de erro a ser retornada.
     * @param array $errors Dados adicionais sobre o erro.
     * @return self Resposta de conflito para o cliente.
     */
    public static function conflict(array $errors, ?string $message = null): self
    {
        return new self(
            status: HttpStatusCode::CONFLICT,
            message: $message,
            errors: $errors
        );
    }

    /**
     * Retorna a resposta de erro interno do servidor para o cliente
     * @param array $errors Dados adicionais sobre o erro.
     * @param string $message Mensagem de erro a ser retornada.
     * @return self Resposta de erro interno do servidor para o cliente.
     */
    public static function internalServerError(array $errors, ?string $message = null): self
    {
        return new self(
            status: HttpStatusCode::INTERNAL_SERVER_ERROR,
            message: $message,
            errors: $errors
        );
    }

    /**
     * Retorna a resposta de erro de autenticação para o cliente
     * @param string $message Mensagem de erro a ser retornada.
     * @param array $errors Dados adicionais sobre o erro.
     * @return self Resposta de erro de autenticação para o cliente.
     */
    public static function unauthorized(string $message, array $errors = []): self
    {
        return new self(
            status: HttpStatusCode::UNAUTHORIZED,
            message: $message,
            errors: $errors
        );
    }

    /**
     * Retorna a resposta de erro de permissão para o cliente
     * @param string $message Mensagem de erro a ser retornada.
     * @param array $errors Dados adicionais sobre o erro.
     * @return self Resposta de erro de permissão para o cliente.
     */
    public static function forbidden(string $message, array $data = []): self
    {
        return new self(
            status: HttpStatusCode::FORBIDDEN,
            message: $message,
            errors: $data
        );
    }
}
