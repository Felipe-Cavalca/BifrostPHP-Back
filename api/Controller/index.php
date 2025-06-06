<?php

namespace Bifrost\Controller;

use Bifrost\Attributes\Cache;
use Bifrost\Attributes\Details;
use Bifrost\Attributes\Method;
use Bifrost\Attributes\RequiredParams;
use Bifrost\Class\HttpResponse;
use Bifrost\Core\Get;
use Bifrost\Core\Request;
use Bifrost\Enum\Field;
use Bifrost\Interface\ControllerInterface;

class Index implements ControllerInterface
{

    #[Cache(20)]
    public function index()
    {
        switch ($_SERVER["REQUEST_METHOD"]) {
            case "OPTIONS":
                return Request::run($this, "options_recurso");
            case "GET":
                return Request::run($this, "get_recurso");
            default:
                return HttpResponse::methodNotAllowed("Method not allowed");
        }
    }

    #[Method("OPTIONS")]
    #[Details(["description" => "Lista informações do recurso"])]
    #[Cache(60)]
    public function options_recurso()
    {
        return HttpResponse::success(
            message: "Recurso",
            data: [
                "list_options" => Request::getOptionsAttributes($this, "options_recurso"),
                "get_recurso" => Request::getOptionsAttributes($this, "get_recurso")
            ]
        );
    }

    #[Method("GET")]
    #[Details([
        "description" => "lista um recurso",
    ])]
    #[Cache(60)]
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
