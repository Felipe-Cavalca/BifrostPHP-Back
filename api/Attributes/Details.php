<?php

namespace Bifrost\Attributes;

use Attribute;
use Bifrost\Interface\AttributesInterface;
use Bifrost\Include\AtrributesDefaultMethods;

#[Attribute]
class Details implements AttributesInterface
{
    use AtrributesDefaultMethods;

    public static array $details = [];

    public function __construct(...$params)
    {
        self::$details = $params[0];
    }

    public function getOptions(): array
    {
        return self::$details;
    }
}
