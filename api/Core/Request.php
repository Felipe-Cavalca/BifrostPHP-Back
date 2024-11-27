<?php

/**
 * It is responsible for initializing the configuration and managing the system's lifecycle.
 *
 * @category Core
 * @copyright 2024
 */

namespace Bifrost\Core;

use Bifrost\Core\Get;
use Bifrost\Core\Settings;
use Bifrost\Enum\Path;
use Bifrost\Class\HttpError;
use Bifrost\Interface\ControllerInterface;
use ReflectionMethod;

/**
 * Class Request
 *
 * This is the main class of the Bifrost system.
 * It is responsible for initializing the configuration and managing the system's lifecycle.
 *
 * @package Bifrost\Core
 * @author Felipe dos S. Cavalca
 */
final class Request
{
    private Get $Get;

    public function __construct()
    {
        $this->Get = new Get();
        Settings::init();
    }

    public function __toString(): string
    {
        return $this->handleResponse($this->run($this->Get->controller, $this->Get->action));
    }

    public static function run(string $controllerName, string $actionName): mixed
    {
        try {
            if (!self::validateControllerName($controllerName)) {
                return HttpError::notFound("Action not found", ["path" => $controllerName]);
            }
            $objController = self::loadController($controllerName);

            if (!self::validateActionName($objController, $actionName)) {
                return HttpError::notFound("Action not found", ["path" => $controllerName . "/" . $actionName]);
            }

            $reflectionMethod = new ReflectionMethod($objController, $actionName);
            $attributes = self::getAttributes($reflectionMethod);
            $return = self::runBeforeAttributes($attributes);

            if ($return !== null) {
                return $return;
            }

            $return = self::runAction($objController, $actionName);
            self::runAfterAttributes($attributes, $return);
            return $return;
        } catch (HttpError $erro) {
            return $erro;
        }
    }

    private static function validateControllerName(string $controller): bool
    {
        $nameController = './Controller/' . $controller . ".php";
        $controller = Path::CONTROLLERS->value . $controller;
        if (!is_readable($nameController) || !class_exists($controller)) {
            return false;
        }
        return true;
    }

    private static function loadController(string $controllerName): ControllerInterface
    {
        $controller = Path::CONTROLLERS->value . $controllerName;
        return new $controller();
    }

    private static function validateActionName(ControllerInterface $controller, string $action): bool
    {
        if (!method_exists($controller, $action)) {
            return false;
        }
        return true;
    }

    private static function getAttributes(ReflectionMethod $reflectionMethod): array
    {
        $attributesReturn = [];
        $attributes = $reflectionMethod->getAttributes();
        foreach ($attributes as $attribute) {
            $attributesReturn[] = $attribute->newInstance();
        }
        return $attributesReturn;
    }

    private static function runBeforeAttributes(array $attributes): mixed
    {
        foreach ($attributes as $attribute) {
            if (method_exists($attribute, "beforeRun")) {
                $retorno = $attribute->beforeRun();
                if ($retorno !== null) {
                    return $retorno;
                }
            }
        }
        return null;
    }

    private static function runAfterAttributes(array|null $attributes, mixed $return): void
    {
        foreach ($attributes as $attribute) {
            if (method_exists($attribute, "afterRun")) {
                $attribute->afterRun($return);
            }
        }
    }

    private static function runAction(ControllerInterface $controller, string $action): mixed
    {
        return call_user_func([$controller, $action]);
    }

    private function handleResponse(mixed $return): string
    {
        if (is_array($return)) {
            return json_encode($return);
        } else {
            return (string) $return;
        }
    }

    public static function getOptionsAttributes($controller, $action): array
    {
        $controller = self::loadController($controller);
        $reflectionMethod = new ReflectionMethod($controller, $action);
        $attributes = $reflectionMethod->getAttributes();
        $options = [];
        foreach ($attributes as $attribute) {
            $attribute = $attribute->newInstance();
            if (method_exists($attribute, "getOptions")) {
                $options = array_merge($options, $attribute->getOptions());
            }
        }
        return $options;
    }
}
