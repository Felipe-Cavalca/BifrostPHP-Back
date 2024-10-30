<?php

namespace Bifrost\Attributes;

use Attribute;
use Bifrost\Class\HttpError;
use Bifrost\Core\Post;
use Bifrost\Enum\ValidateField;
use Bifrost\Include\AtrributesDefaultMethods;
use Bifrost\Interface\AttributesInterface;

#[Attribute]
class RequiredFields implements AttributesInterface
{
    use AtrributesDefaultMethods;

    public static array $fields = [];
    private Post $Post;
    private array $errors = [];

    public function __construct(...$fields)
    {
        self::$fields = $fields[0];
        $this->Post = new Post();
    }

    public function beforeRun(): mixed
    {
        if (!$this->validateRequiredFields(self::$fields)) {
            return HttpError::badRequest("Campos inválidos", $this->getErrors());
        }
        return null;
    }

    public function getOptions(): array
    {
        $campos = [];
        foreach (self::$fields as $field => $filter) {
            $campos[$field] = $filter->value ?? null;
        }
        return ["Campos" => $campos];
    }

    public function validateRequiredFields(array $fields): bool
    {
        $this->errors = [];

        foreach ($fields as $field => $filter) {
            if (is_int($field)) {
                $field = $filter;
                $filter = validateField::DEFAULT;
            }

            if (!static::existField($field)) {
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

    private function existField(string $field): bool
    {
        return isset($this->Post->$field);
    }

    private function validateType(string $field, ValidateField $filter): bool
    {
        return $filter->validate($this->Post->$field);
    }
}
