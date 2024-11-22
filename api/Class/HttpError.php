<?php

namespace Bifrost\Class;

use Bifrost\Enum\HttpStatusCode;
use Bifrost\Class\HttpResponse;

class HttpError extends \Error
{
    private HttpStatusCode $statusCode;
    private string $details;
    private array|string $data;
    private array $additionalInfo;

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

    public function __toString(): string
    {
        return json_encode(HttpResponse::buildResponse(
            statusCode: $this->statusCode,
            message: $this->details,
            data: $this->data,
            additionalInfo: $this->additionalInfo
        ));
    }

    public static function notFound(string $details, array|string $data = []): HttpError
    {
        return new self(
            statusCode: HttpStatusCode::NOT_FOUND,
            details: $details,
            data: $data
        );
    }

    public static function methodNotAllowed(string $details, array|string $data = []): HttpError
    {
        return new self(
            statusCode: HttpStatusCode::METHOD_NOT_ALLOWED,
            details: $details,
            data: $data
        );
    }

    public static function badRequest(string $details, array|string $data = []): HttpError
    {
        return new self(
            statusCode: HttpStatusCode::BAD_REQUEST,
            details: $details,
            data: $data
        );
    }

    public static function internalServerError(string $details, array|string $data = [], array $additionalInfo = []): HttpError
    {
        return new self(
            statusCode: HttpStatusCode::INTERNAL_SERVER_ERROR,
            details: $details,
            data: $data,
            additionalInfo: $additionalInfo
        );
    }

    public static function unauthorized(string $details, array|string $data = []): HttpError
    {
        return new self(
            statusCode: HttpStatusCode::UNAUTHORIZED,
            details: $details,
            data: $data
        );
    }
}
