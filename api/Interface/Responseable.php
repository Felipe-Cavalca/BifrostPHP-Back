<?php

namespace Bifrost\Interface;

use JsonSerializable;

interface Responseable extends JsonSerializable
{

    /**
     * Metodo para serializar o objeto em um array para representação JSON.
     */
    public function jsonSerialize(): array;
}
