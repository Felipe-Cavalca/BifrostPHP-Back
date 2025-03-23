<?php

namespace Bifrost\Include;

use InvalidArgumentException;
use Bifrost\Enum\Field;

trait AbstractFieldValue
{
    protected mixed $value;

    public function init(mixed $value, Field $field): static
    {
        if (!$field->validate($value) || !$this->customValidate($value)) {
            throw new InvalidArgumentException("Valor inválido para o tipo: {$field->name}");
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
            throw new InvalidArgumentException("Valor inválido para o tipo: {$field->name}");
        }
    }

    protected function customValidate(mixed $value): bool
    {
        return true; // por padrão
    }
}
