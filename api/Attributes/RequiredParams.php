<?php

namespace Bifrost\Attributes;

use Attribute;
use Bifrost\Class\HttpError;
use Bifrost\Interface\AttributesInterface;
use Bifrost\Include\AtrributesDefaultMethods;
use Bifrost\Core\Get;
use Bifrost\Enum\Field;

#[Attribute]
class RequiredParams implements AttributesInterface
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

    public function beforeRun(): mixed
    {
        if (!$this->validateRequiredParams(self::$params)) {
            return HttpError::badRequest("Parâmetros inválidos", $this->getErrors());
        }
        return null;
    }

    public function getOptions(): array
    {
        $params = [];
        foreach (self::$params as $field => $filter) {
            $params[$field] = $filter->value ?? null;
        }
        return ["Parâmetros" => $params];
    }

    private function validateRequiredParams(array $params): bool
    {
        $this->errors = [];

        foreach ($params as $field => $filter) {
            if (is_int($field)) {
                $field = $filter;
                $filter = Field::DEFAULT;
            }

            if (!static::existParam($field)) {
                $this->errors[$field] = "Campo não encontrado";
            }

            if (!static::validateType($field, $filter) && empty($this->errors[$field])) {
                $this->errors[$field] = "Tipo de campo inválido";
            }
        }

        if (empty($this->errors)) {
            return true;
        }
        return false;
    }

    private function getErrors(): array
    {
        return $this->errors;
    }

    private function existParam(string $field): bool
    {
        return isset($this->Get->$field);
    }

    private function validateType(string $field, Field $filter): bool
    {
        return $filter->validate($this->Get->$field);
    }
}
