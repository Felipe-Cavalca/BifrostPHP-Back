<?php

namespace Bifrost\Class;

use Bifrost\Enum\HttpStatusCode;

class HttpResponse
{
    private HttpStatusCode $statusCode;
    private string $message;
    private array|string $data;
    private array $additionalInfo;

    public function __construct(
        HttpStatusCode $statusCode = HttpStatusCode::INTERNAL_SERVER_ERROR,
        string $message = "",
        array|string $data = [],
        array $additionalInfo = []
    ) {
        $this->statusCode = $statusCode;
        $this->message = $message;
        $this->data = $data;
        $this->additionalInfo = $additionalInfo;
    }

    public function __toString(): string
    {
        return json_encode(self::buildResponse(
            $this->statusCode,
            $this->message,
            $this->data,
            $this->additionalInfo
        ));
    }

    public static function buildResponse(
        HttpStatusCode $statusCode,
        string $message,
        array|string $data = [],
        array $additionalInfo = []
    ): array {
        $dateTime = new \DateTime();
        $response = [
            "statusCode" => $statusCode->value,
            "isSuccess" => $statusCode->isSuccess(),
            "message" => $message,
            "timestamp" => $dateTime->format('Y-m-d H:i:s.uP'),
        ];

        if (!empty($data)) {
            $response["data"] = is_string($data) ? json_decode($data, true) : $data;
        }

        http_response_code($statusCode->value);
        return array_merge($response, $additionalInfo);
    }

    public static function success(string $message, array|string $data = []): array
    {
        return self::buildResponse(HttpStatusCode::OK, $message, $data);
    }

    public static function notFound(string $message, array|string $data = []): array
    {
        return self::buildResponse(HttpStatusCode::NOT_FOUND, $message, $data);
    }

    public static function options(array $methods = []): array
    {
        header("Access-Control-Allow-Methods: " . implode(", ", $methods));
        return self::buildResponse(
            statusCode: HttpStatusCode::OK,
            message: "Allow Methods",
            additionalInfo: ["methods" => $methods]
        );
    }

    public static function returnAttributes(string $name, array $attributes): array
    {
        return self::buildResponse(
            statusCode: HttpStatusCode::OK,
            message: $name,
            data: $attributes
        );
    }

    // Adicione outros métodos conforme necessário
}
