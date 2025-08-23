<?php

namespace Bifrost\Interface;

use Bifrost\Interface\Responseable;

interface Controller
{

    /**
     * Metodo padrão para caso a URL contenha apenas o controller e nenhuma action
     * @return Responseable Resposta para o usuário
     */
    public function index(): Responseable;
}
