<?php

namespace Bifrost\Attributes;

use Attribute;
use Bifrost\Class\HttpError;
use Bifrost\Interface\AttributesInterface;

#[Attribute]
class RequiredFields implements AttributesInterface
{

    public function __construct(private mixed $fields)
    {
        $this->validateRequiredFields($this->fields);
    }

    public function __destruct() {}

    public function beforeRun(): mixed
    {
        return null;
    }

    public function afterRun($return): void {}

    private function validateRequiredFields(array $fields)
    {
        foreach ($fields as $field => $param) {
            if (is_int($field)) {
                $field = $param;
                $param = FILTER_DEFAULT;
            }
            static::existField($field);
            static::validateType($field, $param);
        }
    }

    private function existField($field)
    {
        if (!isset($_POST[$field])) {
            throw HttpError::badRequest("Campo não encontrado", [
                "fieldName" => $field,
            ]);
        }
    }

    private function validateType($field, $param)
    {
        if (!filter_var($_POST[$field], $param)) {
            throw HttpError::badRequest("Campo inválido", [
                "fieldName" => $field,
            ]);
        }
    }
}
