<?php

/**
 * It is responsible for initializing the configuration and managing the system's lifecycle.
 *
 * @category Core
 * @copyright 2024
 */

namespace Bifrost\Core;

use Bifrost\Class\HttpResponse;
use Bifrost\Core\AppError;
use Bifrost\Core\Get;
use Bifrost\Core\Settings;
use Bifrost\Enum\Path;
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

    /**
     * Executa uma ação de um Controller.
     * @param string|ControllerInterface $controller Nome do Controller.
     * @param string $action Nome da ação do Controller.
     * @return mixed Retorna o resultado da ação do Controller.
     */
    public static function run(string|ControllerInterface $controller, string $action): mixed
    {
        try {

            if ($controller instanceof ControllerInterface) {
                $objController = $controller;
            } else {
                if (empty($controller) || !self::validateControllerName($controller)) {
                    throw new AppError(HttpResponse::notFound(["controller" => $controller], "Controller not found"));
                }

                $objController = self::loadController($controller);
            }

            if (!self::validateActionName($objController, $action)) {
                throw new AppError(HttpResponse::notFound(["action" => $action], "Action not found"));
            }

            $reflectionMethod = new ReflectionMethod($objController, $action);
            $attributes = self::getAttributes($reflectionMethod);
            $return = self::runBeforeAttributes($attributes);

            // Se o método beforeRun retornar algo, não executa a ação do controller.
            if ($return !== null) {
                return $return;
            }

            $return = self::runAction($objController, $action);
            self::runAfterAttributes($attributes, $return);
            return $return;
        } catch (\Throwable $erro) {
            if ($erro instanceof AppError) {
                return $erro;
            }
            return HttpResponse::internalServerError([], $erro->getMessage());
        }
    }

    /**
     * Valida o nome do Controller.
     * @param string $controller Nome do Controller.
     * @return bool Retorna true se o Controller for válido, caso contrário, false.
     */
    private static function validateControllerName(string $controller): bool
    {
        $nameController = Path::FOLDER->toDirectory() . Path::CONTROLLERS->toDirectory() . $controller . ".php";
        $controller = Path::NAMESPACE->value . Path::CONTROLLERS->value . $controller;
        if (!is_readable($nameController) || !class_exists($controller)) {
            return false;
        }
        return true;
    }

    /**
     * Carrega o Controller.
     * @param string $controllerName Nome do Controller.
     * @return ControllerInterface Retorna uma instância do Controller.
     */
    private static function loadController(string $controllerName): ControllerInterface
    {
        $controller = Path::NAMESPACE->value . Path::CONTROLLERS->value . $controllerName;
        return new $controller();
    }

    /**
     * Valida o nome da ação do Controller.
     * @param ControllerInterface $controller Instância do Controller.
     * @param string $action Nome da ação do Controller.
     * @return bool Retorna true se a ação for válida, caso contrário, false.
     */
    private static function validateActionName(ControllerInterface $controller, string $action): bool
    {
        return method_exists($controller, $action);
    }

    /**
     * Obtém os atributos de um método.
     * @param ReflectionMethod $reflectionMethod Método a ser analisado.
     * @return array Retorna um array com os atributos do método.
     */
    private static function getAttributes(ReflectionMethod $reflectionMethod): array
    {
        $attributesReturn = [];
        $attributes = $reflectionMethod->getAttributes();
        foreach ($attributes as $attribute) {
            $attributesReturn[] = $attribute->newInstance();
        }
        return $attributesReturn;
    }

    /**
     * Executa os métodos beforeRun dos atributos.
     * @param array $attributes Atributos do método.
     * @return mixed Retorna o resultado do método beforeRun, se houver.
     * @see AttributesInterface
     */
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

    /**
     * Executa os métodos afterRun dos atributos.
     * @param array|null $attributes Atributos do método.
     * @param mixed $return Retorno da ação do Controller.
     * @return void
     * @see AttributesInterface
     */
    private static function runAfterAttributes(array|null $attributes, mixed $return): void
    {
        foreach ($attributes as $attribute) {
            if (method_exists($attribute, "afterRun")) {
                $attribute->afterRun($return);
            }
        }
    }

    /**
     * Executa a ação do Controller.
     * @param ControllerInterface $controller Instância do Controller.
     * @param string $action Nome da ação do Controller.
     * @return mixed Retorna o resultado da ação do Controller.
     */
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

    /**
     * Obtém os atributos de opções de um Controller e ação.
     * @param string|ControllerInterface $controller Nome do Controller ou instância do Controller.
     * @param string $action Nome da ação do Controller.
     * @return array Retorna um array com as opções dos atributos.
     */
    public static function getOptionsAttributes(string|ControllerInterface $controller, string $action): array
    {
        if (! $controller instanceof ControllerInterface) {
            $controller = self::loadController($controller);
        }

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
