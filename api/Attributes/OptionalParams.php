<?php

namespace Bifrost\Attributes;

use Attribute;
use Bifrost\Class\HttpResponse;
use Bifrost\Interface\Attribute as AttributeInterface;
use Bifrost\Core\Get;
use Bifrost\Enum\Field;
use Bifrost\Interface\AttributeBefore;
use Bifrost\Interface\Responseable;

#[Attribute]
class OptionalParams implements AttributeInterface, AttributeBefore
{

    public static array $params = [];
    private Get $Get;
    private array $errors = [];

    public function __construct(...$params)
    {
        self::$params = $params[0];
        $this->Get = new Get();
    }

    /**
     * Valida os parâmetros opcionais e retorna erro caso algum parâmetro não seja válido.
     * @return null|Responseable Retorna uma resposta em caso de erro ou null se todos os parâmetros forem válidos.
     */
    public function before(): null|Responseable
    {
        if (!$this->validateOptionalParams(self::$params)) {
            return HttpResponse::badRequest(
                errors: $this->getErrors(),
                message: "Invalid parameters"
            );
        }
        return null;
    }

    /**
     * Retorna os parâmetros opcionais para o endpoint.
     * @return array Parâmetros opcionais para o endpoint.
     */
    public function getOptions(): array
    {
        $params = [];
        foreach (self::$params as $field => $filter) {
            $params[$field] = $filter->value ?? null;
        }
        return ["optionalParams" => $params];
    }

    /**
     * Valida os parâmetros opcionais e retorna erro caso algum parâmetro não seja válido.
     * @param array $params - Parâmetros opcionais para o endpoint.
     * @return bool True caso todos os parâmetros sejam válidos, false caso contrário.
     */
    private function validateOptionalParams(array $params): bool
    {
        $this->errors = [];

        foreach ($params as $field => $filter) {
            if (is_int($field)) {
                $field = $filter;
                $filter = Field::DEFAULT;
            }

            if (!static::validateType($field, $filter) && empty($this->errors[$field])) {
                $this->errors[$field] = "Invalid parameter type";
            }
        }

        if (empty($this->errors)) {
            return true;
        }
        return false;
    }

    /**
     * Retorna os erros encontrados na validação dos parâmetros opcionais.
     * @return array - Erros encontrados na validação dos parâmetros opcionais.
     */
    private function getErrors(): array
    {
        return ["params" => $this->errors];
    }

    /**
     * Valida o tipo do parâmetro.
     * @param string $field Parâmetro a ser validado.
     * @param Field $filter Tipo do parâmetro a ser validado.
     * @return bool Retorna true se o tipo do parâmetro for válido, false caso contrário.
     */
    private function validateType(string $field, Field $filter): bool
    {
        if (!isset($this->Get->$field)) {
            return true;
        }
        return $filter->validate($this->Get->$field);
    }
}
