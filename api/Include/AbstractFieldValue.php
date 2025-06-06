<?php

namespace Bifrost\Include;

use Bifrost\Class\HttpResponse;
use Bifrost\Core\AppError;
use Bifrost\Enum\Field;

trait AbstractFieldValue
{
    protected mixed $value;

    public function init(mixed $value, Field $field): static
    {
        if (!$field->validate($value) || !$this->customValidate($value)) {
            throw new AppError(HttpResponse::internalServerError(
                errors: [
                    "value" => "O valor '{$value}' não é válido para o campo {$field->name}",
                    "field" => $field
                ],
                message: "Erro ao validar o campo {$field->name}"
            ));
        }

        $this->value = $value;
        return $this;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    protected function validateField(Field $field, mixed $value): void
    {
        if (!$field->validate($value)) {
            throw new AppError(HttpResponse::internalServerError(
                errors: [
                    "value" => "O valor '{$value}' não é válido para o campo {$field->name}",
                    "field" => $field
                ],
                message: "Erro ao validar o campo {$field->name}"
            ));
        }
    }

    protected function customValidate(mixed $value): bool
    {
        return true; // por padrão
    }
}
