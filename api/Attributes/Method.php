<?php

namespace Bifrost\Attributes;

use Attribute;
use Bifrost\Class\HttpResponse;
use Bifrost\Core\AppError;
use Bifrost\Interface\Attribute as AttributesInterface;
use Bifrost\Core\Request;
use Bifrost\Core\Get;
use Bifrost\Interface\AttributeBefore;
use Bifrost\Interface\Responseable;

#[Attribute]
class Method implements AttributesInterface, AttributeBefore
{

    private static array $methods;
    private Get $Get;

    public function __construct(...$parms)
    {
        self::$methods = $parms;
        $this->Get = new Get();
    }

    public function before(): null|Responseable
    {
        if ($this->isOptions() && !in_array("OPTIONS", self::$methods)) {
            return HttpResponse::success(
                message: "Endpoint information",
                data: [
                    "attributes" => Request::getOptionsAttributes($this->Get->controller, $this->Get->action)
                ]
            );
        }

        // Valida se o método da requisição é permitido.
        if (!$this->validateMethods(self::$methods)) {
            return HttpResponse::methodNotAllowed(message: "The method {$_SERVER["REQUEST_METHOD"]} is not allowed for this endpoint.");
        }

        return null;
    }

    /**
     * Retorna os métodos permitidos para o endpoint.
     * @return array - Métodos permitidos para o endpoint.
     */
    public function getOptions(): array
    {
        return ["methods" => self::$methods];
    }

    /**
     * Valida se o método da requisição é permitido.
     * @param array $methods - Métodos permitidos para o endpoint.
     * @return bool - Retorna true se o método da requisição for permitido, caso contrário false.
     */
    public static function validateMethods(array $methods): bool
    {
        return in_array($_SERVER["REQUEST_METHOD"], $methods);
    }

    public static function isOptions(): bool
    {
        return self::validateMethods(["OPTIONS"]);
    }
}
