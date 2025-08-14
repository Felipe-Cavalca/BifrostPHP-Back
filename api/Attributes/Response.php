<?php

namespace Bifrost\Attributes;

use Attribute;
use Bifrost\Include\AtrributesDefaultMethods;
use Bifrost\Interface\AttributesInterface;

#[Attribute]
class response implements AttributesInterface
{
    use AtrributesDefaultMethods;

    public static array $response = [];

    public function __construct(...$response)
    {
        self::$response = $response[0];
    }

    public function getOptions(): array
    {
        return ["response" => $this->getField(self::$response)];
    }

    public function getField(array $arr): array
    {
        $options = [];
        foreach ($arr as $field => $type) {
            $options[$field] = is_array($type) ? $this->getField($type) : $type;
        }
        return $options;
    }
}
