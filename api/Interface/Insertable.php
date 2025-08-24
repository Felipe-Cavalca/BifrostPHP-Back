<?php

namespace Bifrost\Interface;

interface Insertable
{

    /**
     * Retorna o valor do objeto que deve ser inserido no banco de dados.
     * @return string|int|bool|float|null
     */
    public function value(): string|int|bool|float|null;
}
