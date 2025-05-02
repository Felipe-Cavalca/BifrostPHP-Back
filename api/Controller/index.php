<?php

namespace Bifrost\Controller;

use Bifrost\Attributes\Cache;
use Bifrost\Attributes\Details;
use Bifrost\Attributes\Method;
use Bifrost\Attributes\RequiredParams;
use Bifrost\Class\HttpError;
use Bifrost\Class\HttpResponse;
use Bifrost\Core\Get;
use Bifrost\Core\Request;
use Bifrost\Enum\Field;
use Bifrost\Interface\ControllerInterface;

class Index implements ControllerInterface
{

    #[Cache("index-data", 20)]
    public function index()
    {
        switch ($_SERVER["REQUEST_METHOD"]) {
            case "OPTIONS":
                return Request::run("index", "options_recurso");
            case "GET":
                return Request::run("index", "get_recurso");
            default:
                return HttpError::methodNotAllowed("Method not allowed");
        }
    }

    #[Method("OPTIONS")]
    #[Details(["description" => "Lista informações do recurso"])]
    #[Cache("options_recurso", 60)]
    public function options_recurso()
    {
        return HttpResponse::returnAttributes("informações do recurso", [
            "list_options" => Request::getOptionsAttributes("index", "options_recurso"),
            "get_recurso" => Request::getOptionsAttributes("index", "get_recurso")
        ]);
    }

    #[Method("GET")]
    #[Details([
        "description" => "lista um recurso",
    ])]
    #[Cache("get_recurso", 60)]
    #[RequiredParams([
        "id" => Field::UUID
    ])]
    public function get_recurso()
    {
        $get = new Get();
        return HttpResponse::success("Recurso", [
            "id" => $get->id,
        ]);
    }
}
