<?php

namespace Bifrost\Class;

use Bifrost\Enum\HttpStatusCode;
use Bifrost\Class\HttpResponse;

/**
 * Classe para tratar erros HTTP.
 * @package Bifrost\Class
 * @param HttpStatusCode $statusCode - Código de status HTTP.
 * @param string $details - Detalhes do erro.
 * @param array|string $data - Dados adicionais do erro.
 * @param array $additionalInfo - Informações adicionais do erro.
 */
class HttpError extends \Error
{
    private HttpStatusCode $statusCode;
    private string $details;
    private array|string $data;
    private array $additionalInfo;

    /**
     * Construtor da classe HttpError.
     * @param HttpStatusCode $statusCode - Código de status HTTP.
     * @param string $details - Detalhes do erro.
     * @param array|string $data - Dados adicionais do erro.
     * @param array $additionalInfo - Informações adicionais do erro.
     */
    public function __construct(
        HttpStatusCode $statusCode = HttpStatusCode::INTERNAL_SERVER_ERROR,
        string $details = "",
        array|string $data = [],
        array $additionalInfo = []
    ) {
        $this->statusCode = $statusCode;
        $this->details = $details;
        $this->data = $data;
        $this->additionalInfo = $additionalInfo;
        parent::__construct($details);
    }

    /**
     * Retorna o json do erro.
     * @return string - Json do erro.
     */
    public function __toString(): string
    {
        $additionalInfo = array_merge($this->additionalInfo, [
            "help" => "for more information send this request with OPTIONS method"
        ]);
        return json_encode(HttpResponse::buildResponse(
            statusCode: $this->statusCode,
            message: $this->details,
            data: $this->data,
            additionalInfo: $additionalInfo
        ));
    }

    /**
     * Retorna a resposta de não encontrado para o cliente
     * @return HttpError - Resposta de não encontrado para o cliente.
     */
    public static function notFound(string $details, array|string $data = []): HttpError
    {
        return new self(
            statusCode: HttpStatusCode::NOT_FOUND,
            details: $details,
            data: $data
        );
    }

    /**
     * Retorna a resposta de método não permitido para o cliente
     * @return HttpError - Resposta de método não permitido para o cliente.
     */
    public static function methodNotAllowed(string $details, array|string $data = []): HttpError
    {
        return new self(
            statusCode: HttpStatusCode::METHOD_NOT_ALLOWED,
            details: $details,
            data: $data
        );
    }

    /**
     * Retorna a resposta de erro de requisição para o cliente
     * @return HttpError - Resposta de erro de requisição para o cliente.
     */
    public static function badRequest(string $details, array|string $data = []): HttpError
    {
        return new self(
            statusCode: HttpStatusCode::BAD_REQUEST,
            details: $details,
            data: $data
        );
    }

    /**
     * Retorna a resposta de erro interno do servidor para o cliente
     * @return HttpError - Resposta de erro interno do servidor para o cliente.
     */
    public static function internalServerError(string $details, array|string $data = [], array $additionalInfo = []): HttpError
    {
        return new self(
            statusCode: HttpStatusCode::INTERNAL_SERVER_ERROR,
            details: $details,
            data: $data,
            additionalInfo: $additionalInfo
        );
    }

    /**
     * Retorna a resposta de erro de autenticação para o cliente
     * @return HttpError - Resposta de erro de autenticação para o cliente.
     */
    public static function unauthorized(string $details, array|string $data = []): HttpError
    {
        return new self(
            statusCode: HttpStatusCode::UNAUTHORIZED,
            details: $details,
            data: $data
        );
    }

    /**
     * Retorna a resposta de erro de permissão para o cliente
     * @return HttpError - Resposta de erro de permissão para o cliente.
     */
    public static function forbidden(string $details, array|string $data = []): HttpError
    {
        return new self(
            statusCode: HttpStatusCode::FORBIDDEN,
            details: $details,
            data: $data
        );
    }
}
