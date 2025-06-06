<?php

namespace Bifrost\Core;

use Bifrost\Class\HttpResponse;
use \Exception;

/**
 * É responsável por disparar erros de aplicação.
 * @property HttpResponse $response
 */
class AppError extends Exception
{
    public HttpResponse $response;

    /**
     * AppError constructor.
     * @param HttpResponse $response
     */
    public function __construct(HttpResponse $response)
    {
        $this->response = $response;
        parent::__construct((string)$response);
    }

    /**
     * Retorna a mensagem de erro.
     * @return string
     */
    public function __toString(): string
    {
        return $this->response;
    }
}
