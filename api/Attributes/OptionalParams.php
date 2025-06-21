<?php

namespace Bifrost\Attributes;

use Attribute;
use Bifrost\Class\HttpResponse;
use Bifrost\Interface\AttributesInterface;
use Bifrost\Include\AtrributesDefaultMethods;
use Bifrost\Core\Get;
use Bifrost\Enum\Field;

/**
 * Parâmetros opcionais para o endpoint.
 * @param array $params - Parâmetros opcionais para o endpoint.
 * Recebe o array sendo o índice no GET e o valor o tipo do campo.
 */
#[Attribute]
class OptionalParams implements AttributesInterface
{
    use AtrributesDefaultMethods;

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
     * @return mixed - Erro de requisição ou null caso não tenha erro.
     */
    public function beforeRun(): mixed
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
     * @return array - Parâmetros opcionais para o endpoint.
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
     * @return bool - True caso todos os parâmetros sejam válidos, false caso contrário.
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
     * Valida se o parâmetro existe no GET.
     * @param string $field - Parâmetro a ser validado.
     * @return bool - Retorna true se o parâmetro existir, false caso contrário.
     */
    private function existParam(string $field): bool
    {
        return isset($this->Get->$field);
    }

    /**
     * Valida o tipo do parâmetro.
     * @param string $field - Parâmetro a ser validado.
     * @param Field $filter - Tipo do parâmetro a ser validado.
     * @return bool - Retorna true se o tipo do parâmetro for válido, false caso contrário.
     */
    private function validateType(string $field, Field $filter): bool
    {
        if (!isset($this->Get->$field)) {
            return true;
        }
        return $filter->validate($this->Get->$field);
    }
}
