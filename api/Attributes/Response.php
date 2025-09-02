<?php

namespace Bifrost\Attributes;

use Attribute;
use Bifrost\Interface\Attribute as AttributeInterface;

#[Attribute]
class Response implements AttributeInterface
{
    public static array $response = [];

    public function __construct(...$response)
    {
        self::$response = $response[0];
    }

    public function getOptions(): array
    {
        return ["response" => $this->getField(self::$response)];
    }

    /**
     * Processa a estrutura de resposta e retorna as opções formatadas.
     * @return array Retorna as opções formatadas da estrutura de resposta.
     */
    public function getField(array $arr): array
    {
        $options = [];
        foreach ($arr as $field => $type) {
            $options[$field] = is_array($type) ? $this->getField($type) : $type;
        }
        return $options;
    }
}
