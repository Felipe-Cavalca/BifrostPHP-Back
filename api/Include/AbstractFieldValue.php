<?php

namespace Bifrost\Include;

use Bifrost\Class\HttpResponse;
use Bifrost\Core\AppError;
use Bifrost\Enum\Field;

trait AbstractFieldValue
{
    protected mixed $value;

    protected function init(mixed $value, Field $field): static
    {
        if (!$field->validate($value) || !$this->customValidate($value)) {
            throw new AppError(HttpResponse::internalServerError(
                errors: [
                    "value" => "The value '{$value}' is not valid for the field {$field->name}",
                    "field" => $field
                ],
                message: "Erro ao validar o campo {$field->name}"
            ));
        }

        $this->value = $value;
        return $this;
    }

    protected function customValidate(mixed $value): bool
    {
        return true; // por padrão
    }
}
