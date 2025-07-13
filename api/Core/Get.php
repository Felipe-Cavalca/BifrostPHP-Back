<?php

namespace Bifrost\Core;

use Bifrost\Enum\Routes;

class Get
{
    private static array $data;
    public static string $controller;
    public static string $action;

    public function __construct()
    {
        if (! $_GET instanceof Get) {

            // Define o controlador e a ação padrão
            $path = $_GET["_controller"] ?? "index";
            $action = empty($_GET["_action"]) ? "index" : $_GET["_action"];

            // Verifica se está nas rotas mapeadas
            $route = Routes::fromRequest($path);

            if ($route) {
                // Divide "Controller/action"
                [$controller, $action] = explode("/", $route->value);
            } else {
                $controller = $path;
            }

            // setando o controller e a ação
            self::$controller = $controller;
            self::$action = $action;

            // Remove os parâmetros de controle e ação do array $_GET
            unset($_GET["_controller"], $_GET["_action"]);

            // Armazena os dados restantes em uma propriedade estática
            self::$data = $_GET;

            // Define $_GET como uma instância desta classe
            $_GET = $this;
        }
    }

    public function __toString()
    {
        return json_encode(array_merge(self::$data, [
            "_controller" => self::$controller,
            "_action" => self::$action
        ]), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function __get($name)
    {
        switch ($name) {
            case "controller":
                return self::$controller;
            case "action":
                return self::$action;
            default:
                return self::$data[$name] ?? null;
        }
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case "controller":
                self::$controller = $value;
                break;
            case "action":
                self::$action = $value;
                break;
            default:
                self::$data[$name] = $value;
        }
    }

    public function __isset($name)
    {
        return isset(self::$data[$name]);
    }

    public function __unset($name)
    {
        unset(self::$data[$name]);
    }
}
