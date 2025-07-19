<?php

namespace Bifrost\Interface;

interface TaskInterface
{
    public function __serialize(): array;

    public function __unserialize(array $data): void;

    public function run(): bool;
}
