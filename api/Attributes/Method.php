<?php

namespace Bifrost\Attributes;

use Attribute;
use Bifrost\Interface\AttributesInterface;
use Bifrost\Class\HttpError;

#[Attribute]
class Method implements AttributesInterface
{

    public function __construct(private mixed $methods)
    {
        if (is_array($this->methods)) {
            $this->validateMethods($this->methods);
        } else {
            $this->validateMethod($this->methods);
        }
    }

    public function __destruct() {}

    public function beforeRun(): mixed
    {
        return null;
    }

    public function afterRun($return): void {}

    private function validateMethods(array $methods)
    {
        if (!in_array($_SERVER["REQUEST_METHOD"], $methods)) {
            throw HttpError::methodNotAllowed("Method not allowed");
        }
    }

    private function validateMethod(string $method)
    {
        if ($_SERVER["REQUEST_METHOD"] != $method) {
            throw HttpError::methodNotAllowed("Method not allowed");
        }
    }
}
