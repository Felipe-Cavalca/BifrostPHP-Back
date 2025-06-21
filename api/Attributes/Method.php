<?php

namespace Bifrost\Attributes;

use Attribute;
use Bifrost\Class\HttpResponse;
use Bifrost\Core\AppError;
use Bifrost\Include\AtrributesDefaultMethods;
use Bifrost\Interface\AttributesInterface;
use Bifrost\Core\Request;
use Bifrost\Core\Get;

/**
 * Método da requisição HTTP.
 * @param string ...$parms - Métodos permitidos para o endpoint.
 */
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

    /**
     * Classifica o método da requisição e retorna os detalhes do endpoint.
     * @return mixed - Detalhes do endpoint ou erro de método não permitido.
     */
    public function beforeRun(): mixed
    {
        // Caso o método seja OPTIONS e o endpoint não receba o OPTIONS retorna os dados do endpoint.
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
            throw new AppError(HttpResponse::methodNotAllowed("The method {$_SERVER["REQUEST_METHOD"]} is not allowed for this endpoint."));
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

    /**
     * Valida se o método da requisição é OPTIONS.
     * @return bool - Retorna true se o método da requisição for OPTIONS, caso contrário false.
     */
    public static function isOptions(): bool
    {
        return self::validateMethods(["OPTIONS"]);
    }
}
