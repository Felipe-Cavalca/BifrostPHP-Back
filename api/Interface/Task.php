<?php

namespace Bifrost\Interface;

interface Task
{

    /**
     * Serializa o objeto em um array para armazenamento
     * @return array
     */
    public function __serialize(): array;

    /**
     * Deserializa o objeto a partir de um array
     * @param array $data
     */
    public function __unserialize(array $data): void;

    /**
     * Executa a lógica da tarefa
     * @return bool true em caso de sucesso ou false em caso de falha
     */
    public function run(): bool;
}
