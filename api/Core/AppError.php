<?php

namespace Bifrost\Core;

use Bifrost\Class\HttpResponse;
use Throwable;

/**
 * É responsável por disparar erros de aplicação.
 * @property HttpResponse $response
 */
class AppError extends \Throwable
{
    public HttpResponse $response;

    /**
     * AppError constructor.
     * @param HttpResponse $response
     */
    public function __construct(HttpResponse $response)
    {
        $this->response = $response;
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
