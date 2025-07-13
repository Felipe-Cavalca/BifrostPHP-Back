<?php

namespace Bifrost\Enum;

Enum Routes: string
{

    /**
     * Enumeração de rotas do sistema.
     * Cada rota é definida como um caso do enum, representando o caminho da rota.
     */
    case login = "auth/login";
    case logout = "auth/logout";

    /**
     * Método para obter o controlador associado a uma rota.
     * @param string $path
     * @return self|null
     */
    public static function fromRequest(string $path): ?self
    {
        foreach (self::cases() as $route) {
            if ($route->name === $path) {
                return $route;
            }
        }
        return null;
    }
}
