<?php

/**
 * It is responsible for initializing the configuration and managing the system's lifecycle.
 *
 * @category Core
 * @copyright 2024
 */

namespace Bifrost\Core;

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
    /** Controller to be executed. */
    private string $controller = "index";

    /** Action to be executed. */
    private string $action = "index";

    /**
     * Request constructor.
     *
     * It initializes the configuration and sanitizes the GET and POST data.
     * @uses Settings::init()
     * @uses Request::sanitizeGet()
     * @uses Request::sanitizePost()
     * @return void
     */
    public function __construct()
    {
        Settings::init();
        $this->sanitizeGet();
        $this->sanitizePost();
    }

    /**
     * It is responsible for returning the response of the system.
     * @uses Request::run()
     * @uses Request::handleResponse()
     * @return string
     */
    public function __toString(): string
    {
        return $this->handleResponse($this->run());
    }

    /**
     * It is responsible for sanitizing the GET data.
     * and setting the controller and action to be executed.
     *
     * @uses Request::$controller
     * @uses Request::$action
     * @global array $_GET
     * @return void
     */
    private function sanitizeGet(): void
    {
        $get = $_GET;
        $url = explode("/", $get["_PageBifrost"] ?? "");

        $this->controller = count($url) == 2 ? $url[0] : $this->controller;
        $this->action = count($url) == 2 ? $url[1] : $url[0];

        unset($get["_PageBifrost"]);
        $_GET = $get;
    }

    /**
     * It is responsible for sanitizing the POST data.
     * and setting the POST data.
     *
     * @global array $_POST
     * @return void
     */
    private function sanitizePost(): void
    {
        $post = $_POST;
        $json = json_decode(file_get_contents('php://input'), true);
        $_POST = (is_array($json) ? $json : $post);
    }

    /**
     * It is responsible for executing the controller and action.
     *
     * @uses Request::validateController()
     * @uses Request::loadController()
     * @uses Request::validateAction()
     * @uses Request::getAttributes()
     * @uses Request::runBeforeAttributes()
     * @uses Request::runAction()
     * @uses Request::runAfterAttributes()
     * @uses HttpError
     *
     * @return mixed The return of the controller and action.
     */
    private function run(): mixed
    {
        try {
            $this->validateController($this->controller);
            $objController = $this->loadController($this->controller);
            $this->validateAction($objController, $this->action);

            $reflectionMethod = new ReflectionMethod($objController, $this->action);
            $attributes = $this->getAttributes($reflectionMethod);
            $return = $this->runBeforeAttributes($attributes);

            if ($return !== null) {
                return $return;
            }

            $return = $this->runAction($objController, $this->action);
            $this->runAfterAttributes($attributes, $return);
            return $return;
        } catch (HttpError $erro) {
            return $erro;
        }
    }

    /**
     * It is responsible for validating the controller.
     *
     * @param string $controller Name of the controller to be validated.
     * @uses Path::CONTROLLERS
     * @throws HttpError Erro 404
     * @return bool
     */
    private function validateController(string $controller): bool
    {
        $controller = Path::CONTROLLERS->value . $controller;
        if (!class_exists($controller)) {
            throw HttpError::notFound("Controller not found", ["controller" => $controller]);
        }
        return true;
    }

    /**
     * It is responsible for loading the controller.
     *
     * @param string $controller Name of the controller to be loaded.
     * @uses Path::CONTROLLERS
     * @return ControllerInterface The controller loaded.
     * @todo Validate if the controller is an instance of Controller.
     */
    private function loadController(string $controller): ControllerInterface
    {
        $controller = Path::CONTROLLERS->value . $controller;
        return new $controller();
    }

    /**
     * It is responsible for validating if the action exists in the controller.
     *
     * @param ControllerInterface $controller Controller to be validated.
     * @param string $action Action to be validated.
     * @throws HttpError Erro 404
     * @return bool
     */
    private function validateAction(ControllerInterface $controller, string $action): bool
    {
        if (!method_exists($controller, $action)) {
            throw HttpError::notFound("Action not found", ["controller" => $controller, "action" => $action]);
        }
        return true;
    }

    /**
     * It is responsible for returning the attributes of the action.
     *
     * @param ReflectionMethod $reflectionMethod Reflection of the action.
     * @return array Attributes of the action.
     */
    private function getAttributes(ReflectionMethod $reflectionMethod): array
    {
        $attributesReturn = [];
        $attributes = $reflectionMethod->getAttributes();
        foreach ($attributes as $attribute) {
            $attributesReturn[] = $attribute->newInstance();
        }
        return $attributesReturn;
    }

    /**
     * It is responsible for executing the attributes before the action.
     *
     * @param array $attributes Attributes to be executed.
     *
     * @uses Attribute::beforeRun()
     *
     * @return mixed The return of the attribute.
     */
    private function runBeforeAttributes(array $attributes): mixed
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
     * It is responsible for executing the attributes after the action.
     *
     * @param array $attributes Attributes to be executed.
     * @param mixed $return Return of the action.
     *
     * @uses Attribute::afterRun()
     *
     * @return void
     */
    private function runAfterAttributes($attributes, $return): void
    {
        foreach ($attributes as $attribute) {
            if (method_exists($attribute, "afterRun")) {
                $attribute->afterRun($return);
            }
        }
    }

    /**
     * It is responsible for executing the action.
     *
     * @param ControllerInterface $controller Controller to be executed.
     * @param string $action Action to be executed.
     * @return mixed The return of the action.
     */
    private function runAction(ControllerInterface $controller, string $action): mixed
    {
        return call_user_func([$controller, $action]);
    }

    /**
     * It is responsible for handling the response.
     *
     * @param mixed $return Return of the system.
     * @return string The response of the system.
     */
    private function handleResponse(mixed $return): string
    {
        if (is_array($return)) {
            return json_encode($return);
        } else {
            return (string) $return;
        }
    }
}
