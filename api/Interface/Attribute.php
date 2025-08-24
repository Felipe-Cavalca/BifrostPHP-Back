<?php

namespace Bifrost\Interface;

interface Attribute
{
     /**
     * Inicializa o atributo com os parâmetros fornecidos.
     * @param mixed ...$params Parâmetros para configurar o atributo.
     */
    public function __construct(...$params);

    /**
     * Retorna as opções configuradas e detalhes do atributo.
     * @return array Lista de opções configuradas e seus detalhes.
     */
    public function getOptions(): array;
}
