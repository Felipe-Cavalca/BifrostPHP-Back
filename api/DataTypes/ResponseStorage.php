<?php

namespace Bifrost\DataTypes;

use Bifrost\Enum\Field;
use Bifrost\Include\AbstractFieldValue;

class ResponseStorage
{
    use AbstractFieldValue;

    public function __construct(mixed $response)
    {
        $this->init($response, Field::ARRAY);
    }

    public function __toString(): string
    {
        return json_encode($this->value);
    }

    public function customValidate(mixed $response): bool
    {
        return isset($response['status']) && isset($response['message']);
    }
}
