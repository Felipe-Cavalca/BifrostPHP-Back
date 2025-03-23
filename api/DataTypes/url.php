<?php

namespace Bifrost\DataTypes;

use Bifrost\Enum\Field;
use Bifrost\Include\AbstractFieldValue;

class Url
{
    use AbstractFieldValue;

    public function __construct(mixed $url)
    {
        $this->init($url, Field::URL);
    }
}
