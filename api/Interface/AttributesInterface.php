<?php

namespace Bifrost\Interface;

interface AttributesInterface
{
    public function __construct(...$params);

    public function __destruct();

    public function beforeRun(): mixed;

    public function afterRun($return): void;

    public function getOptions(): array;
}
