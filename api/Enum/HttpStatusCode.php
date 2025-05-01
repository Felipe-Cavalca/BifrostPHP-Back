<?php

namespace Bifrost\Enum;

/**
 * Enum para representar os códigos de status HTTP.
 */
enum HttpStatusCode: int
{
    case OK = 200;
    case CREATED = 201;
    case ACCEPTED = 202;
    case NO_CONTENT = 204;
    case MOVED_PERMANENTLY = 301;
    case FOUND = 302;
    case NOT_MODIFIED = 304;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case CONFLICT = 409;
    case INTERNAL_SERVER_ERROR = 500;
    case NOT_IMPLEMENTED = 501;
    case BAD_GATEWAY = 502;
    case SERVICE_UNAVAILABLE = 503;

    /**
     * Retorna a mensagem associada ao código de status HTTP.
     */
    public function message(): string
    {
        return match ($this) {
            self::OK => "OK",
            self::CREATED => "Created",
            self::ACCEPTED => "Accepted",
            self::NO_CONTENT => "No Content",
            self::MOVED_PERMANENTLY => "Moved Permanently",
            self::FOUND => "Found",
            self::NOT_MODIFIED => "Not Modified",
            self::BAD_REQUEST => "Bad Request",
            self::UNAUTHORIZED => "Unauthorized",
            self::FORBIDDEN => "Forbidden",
            self::NOT_FOUND => "Not Found",
            self::METHOD_NOT_ALLOWED => "Method Not Allowed",
            self::CONFLICT => "Conflict",
            self::INTERNAL_SERVER_ERROR => "Internal Server Error",
            self::NOT_IMPLEMENTED => "Not Implemented",
            self::BAD_GATEWAY => "Bad Gateway",
            self::SERVICE_UNAVAILABLE => "Service Unavailable",
        };
    }

    /**
     * Verifica se o código de status indica sucesso.
     */
    public function isSuccess(): bool
    {
        return match ($this) {
            self::OK, self::CREATED, self::ACCEPTED, self::NO_CONTENT => true,
            default => false,
        };
    }

    /**
     * Verifica se o código de status indica redirecionamento.
     */
    public function isRedirection(): bool
    {
        return match ($this) {
            self::MOVED_PERMANENTLY, self::FOUND, self::NOT_MODIFIED => true,
            default => false,
        };
    }

    /**
     * Verifica se o código de status indica erro do cliente.
     */
    public function isClientError(): bool
    {
        return match ($this) {
            self::BAD_REQUEST, self::UNAUTHORIZED, self::FORBIDDEN, self::NOT_FOUND, self::METHOD_NOT_ALLOWED, self::CONFLICT => true,
            default => false,
        };
    }

    /**
     * Verifica se o código de status indica erro do servidor.
     */
    public function isServerError(): bool
    {
        return match ($this) {
            self::INTERNAL_SERVER_ERROR, self::NOT_IMPLEMENTED, self::BAD_GATEWAY, self::SERVICE_UNAVAILABLE => true,
            default => false,
        };
    }
}
