<?php

namespace Bifrost\Attributes;

use Attribute;
use Bifrost\Class\HttpError;
use Bifrost\Class\HttpResponse;
use Bifrost\Include\AtrributesDefaultMethods;
use Bifrost\Interface\AttributesInterface;
use Bifrost\Core\Request;
use Bifrost\Core\Get;

#[Attribute]
class Method implements AttributesInterface
{
    use AtrributesDefaultMethods;

    private static array $methods;
    private Get $Get;

    public function __construct(...$parms)
    {
        self::$methods = $parms;
        $this->Get = new Get();
    }

    public function beforeRun(): mixed
    {
        if ($this->isOptions() && !in_array("OPTIONS", self::$methods)) {
            return HttpResponse::returnAttributes(
                name: "Informações do endpoint",
                attributes: Request::getOptionsAttributes($this->Get->controller, $this->Get->action)
            );
        }

        if (!$this->validateMethods(self::$methods)) {
            return HttpError::methodNotAllowed("Method not allowed");
        }

        return null;
    }

    public function getOptions(): array
    {
        return ["Methods" => self::$methods];
    }

    public static function validateMethods(array $methods): bool
    {
        return in_array($_SERVER["REQUEST_METHOD"], $methods);
    }

    public static function isOptions(): bool
    {
        return self::validateMethods(["OPTIONS"]);
    }
}
