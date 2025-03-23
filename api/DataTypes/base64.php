<?php

namespace Bifrost\DataTypes;

use Bifrost\Enum\Field;
use Bifrost\Include\AbstractFieldValue;

class Base64
{
    use AbstractFieldValue;

    public function __construct(mixed $base64)
    {
        $this->init($base64, Field::BASE64);
    }
}
