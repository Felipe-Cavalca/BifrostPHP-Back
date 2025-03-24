<?php

namespace Bifrost\Include;

use Bifrost\Class\HttpError;
use Bifrost\Enum\Field;

trait AbstractFieldValue
{
    protected mixed $value;

    public function init(mixed $value, Field $field): static
    {
        if (!$field->validate($value) || !$this->customValidate($value)) {
            throw HttpError::internalServerError("Houve um erro ao validar o campo {$field->name}", [
                "value" => $value,
                "field" => $field
            ]);
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
            throw HttpError::internalServerError("Houve um erro ao validar o campo {$field->name}", [
                "value" => $value,
                "field" => $field
            ]);
        }
    }

    protected function customValidate(mixed $value): bool
    {
        return true; // por padrão
    }
}
