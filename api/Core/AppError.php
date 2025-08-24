<?php

namespace Bifrost\Core;

use Bifrost\Class\HttpResponse;
use \Exception;

/**
 * Dispara uma exceção personalizada com base em uma resposta HTTP
 * @property HttpResponse $response
 */
class AppError extends Exception
{
    public HttpResponse $response;

    /**
     * Encapsula a resposta HTTP em uma exceção personalizada
     * @param HttpResponse resposta HTTP
     */
    public function __construct(HttpResponse $response)
    {
        $this->response = $response;
        parent::__construct((string) $response->message ?? "ERROR");
    }
}
