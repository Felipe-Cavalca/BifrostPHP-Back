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
                    "value" => $this->errorMessage($field, $value),
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
                    "value" => $this->errorMessage($field, $value),
                    "field" => $field
                ],
                message: "Erro ao validar o campo {$field->name}"
            ));
        }
    }

    private function errorMessage(Field $field, mixed $value): string
    {
        return "The value '{$value}' is not valid for the field {$field->name}";
    }

    protected function customValidate(mixed $value): bool
    {
        return true; // por padrão
    }

    public function jsonSerialize(): mixed
    {
        return $this->value;
    }
}
