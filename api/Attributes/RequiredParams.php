<?php

namespace Bifrost\Attributes;

use Attribute;
use Bifrost\Class\HttpError;
use Bifrost\Interface\AttributesInterface;

#[Attribute]
class RequiredParams implements AttributesInterface
{

    public function __construct(private mixed $params)
    {
        $this->validateRequiredParams($this->params);
    }

    public function __destruct() {}

    public function beforeRun(): mixed
    {
        return null;
    }

    public function afterRun($return): void {}

    private function validateRequiredParams(array $params)
    {
        foreach ($params as $field => $param) {
            if (is_int($field)) {
                $field = $param;
                $param = FILTER_DEFAULT;
            }
            static::existParam($field);
            static::validateType($field, $param);
        }
    }

    private function existParam($field)
    {
        if (!isset($_GET[$field])) {
            throw HttpError::badRequest("Parametro não encontrado", [
                "fieldName" => $field,
            ]);
        }
    }

    private function validateType($field, $param)
    {
        if (!filter_var($_GET[$field], $param)) {
            throw HttpError::badRequest("Parametro inválido", [
                "fieldName" => $field,
            ]);
        }
    }
}
