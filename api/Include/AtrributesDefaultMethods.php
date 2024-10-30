<?php

namespace Bifrost\Include;

trait AtrributesDefaultMethods
{
    public function __construct(...$params) {}

    public function __destruct() {}

    public function beforeRun(): mixed
    {
        return null;
    }

    public function afterRun($return): void {}

    public static function getOptions(): array
    {
        return [];
    }
}
