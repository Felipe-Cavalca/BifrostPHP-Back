<?php

namespace Bifrost\Interface;

/**
 * Interface com as funções que os atributos devem implementar para serem utilizados antes do controller e depois do controller.
 * @package Bifrost\Interface
 */
interface AttributesInterface
{
    /**
     * Função construtora que recebe os parâmetros do atributo.
     * @param mixed ...$params
     * @return void
     */
    public function __construct(...$params);

    /**
     * Função destrutora que é chamada quando o atributo é destruído.
     * Após a execução de tudo
     * @return void
     */
    public function __destruct();

    /**
     * Função que é chamada antes da execução do controller.
     * @return mixed
     * Caso a função retorne algo, o controller não será executado e o retorno será o que foi retornado na função.
     */
    public function beforeRun(): mixed;

    /**
     * Função que é chamada após a execução do controller.
     * @param mixed $return - Retorno do controller.
     * @return void
     */
    public function afterRun($return): void;

    /**
     * Função que retorna as opções do atributo.
     * Função executada quando a request é feita com o metodo OPTIONS.
     * @return array
     */
    public function getOptions(): array;
}
