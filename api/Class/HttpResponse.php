<?php

namespace Bifrost\Class;

use Bifrost\Enum\HttpStatusCode;

/**
 * Classe para tratar respostas HTTP.
 * @package Bifrost\Class
 * @param HttpStatusCode $statusCode - Código de status HTTP.
 * @param string $message - Mensagem da resposta.
 * @param array|string $data - Dados da resposta.
 * @param array $additionalInfo - Informações adicionais da resposta.
 */
class HttpResponse
{
    private HttpStatusCode $statusCode;
    private string $message;
    private array|string $data;
    private array $additionalInfo;

    /**
     * Construtor da classe HttpResponse.
     * @param HttpStatusCode $statusCode - Código de status HTTP.
     * @param string $message - Mensagem da resposta.
     * @param array|string $data - Dados da resposta.
     * @param array $additionalInfo - Informações adicionais da resposta.
     * @return void
     */
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

    /**
     * Retorna o json da resposta.
     * @return string - Json da resposta.
     */
    public function __toString(): string
    {
        return json_encode(self::buildResponse(
            $this->statusCode,
            $this->message,
            $this->data,
            $this->additionalInfo
        ));
    }

    /**
     * Constrói a resposta HTTP.
     * @param HttpStatusCode $statusCode - Código de status HTTP.
     * @param string $message - Mensagem da resposta.
     * @param array|string $data - Dados da resposta.
     * @param array $additionalInfo - Informações adicionais da resposta.
     * @return array - Resposta HTTP.
     */
    public static function buildResponse(
        HttpStatusCode $statusCode,
        string $message,
        array|string $data = [],
        array $additionalInfo = []
    ): array {
        $response = [
            "statusCode" => $statusCode->value,
            "isSuccess" => $statusCode->isSuccess(),
            "message" => $message,
        ];

        if (!empty($data)) {
            $response["data"] = is_string($data) ? json_decode($data, true) : $data;
        }

        http_response_code($statusCode->value);
        return array_merge($response, $additionalInfo);
    }

    /**
     * Retorna a resposta de sucesso para o cliente.
     * @param string $message - Mensagem de sucesso.
     * @param array|string $data - Dados de sucesso.
     * @return array - Resposta de sucesso para o cliente.
     */
    public static function success(string $message, array|string $data = []): array
    {
        return self::buildResponse(HttpStatusCode::OK, $message, $data);
    }

    /**
     * Retorna as opções de métodos permitidos para o cliente.
     * @param array $methods - Métodos permitidos.
     * @return array - Resposta de opções de métodos permitidos para o cliente.
     */
    public static function options(array $methods = []): array
    {
        header("Access-Control-Allow-Methods: " . implode(", ", $methods));
        return self::buildResponse(
            statusCode: HttpStatusCode::OK,
            message: "Allow Methods",
            additionalInfo: ["methods" => $methods]
        );
    }

    /**
     * Retorna a resposta de atributos para o cliente.
     * @param string $name - Nome do atributo.
     * @param array $attributes - Atributos do cliente.
     * @return array - Resposta de atributos para o cliente.
     */
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
