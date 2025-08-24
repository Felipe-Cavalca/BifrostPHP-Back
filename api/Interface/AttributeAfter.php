<?php

namespace Bifrost\Interface;

use Bifrost\Interface\Attribute;
use Bifrost\Interface\Responseable;

interface AttributeAfter extends Attribute
{

    /**
     * Executa uma ação após o processamento principal.
     * @param Responseable $response Objeto de resposta processado.
     * @return void
     */
    public function after(Responseable $response): void;
}
