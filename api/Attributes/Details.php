<?php

namespace Bifrost\Attributes;

use Attribute;
use Bifrost\Interface\AttributesInterface;
use Bifrost\Include\AtrributesDefaultMethods;

/**
 * Detalhes adicionais a serem mostrados caso a requisição seja OPTION.
 * @param array $params - Detalhes adicionais a serem mostrados.
 */
#[Attribute]
class Details implements AttributesInterface
{
    use AtrributesDefaultMethods;

    /**
     * @var array - Detalhes extras da execução passados por parâmetro.
     */
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
