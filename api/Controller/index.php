<?php

namespace Bifrost\Controller;

use Bifrost\Attributes\Cache;
use Bifrost\Attributes\Details;
use Bifrost\Attributes\Method;
use Bifrost\Attributes\RequiredFields;
use Bifrost\Attributes\RequiredParams;
use Bifrost\Class\HttpError;
use Bifrost\Class\HttpResponse;
use Bifrost\Core\Post;
use Bifrost\Core\Request;
use Bifrost\Core\Settings;
use Bifrost\Enum\HttpStatusCode;
use Bifrost\Include\Controller;
use Bifrost\Interface\ControllerInterface;
use Bifrost\Enum\ValidateField;

class Index implements ControllerInterface
{
    use Controller;

    #[Method(["GET"])]
    #[Cache("index-getVars", 10)]
    public function getVars()
    {
        $settings = new Settings();
        return $settings->app;
    }

    #[Method(["GET"])]
    // #[Cache("index-data", 10)]
    public function index()
    {
        if (!isset($_GET["id"])) {
            throw new HttpError(HttpStatusCode::BAD_REQUEST, "ID não informado");
        }

        if ($_GET["id"] == 1) {
            return HttpResponse::success("Sucesso", ["id" => 1, "nome" => "João"]);
        }

        if ($_GET["id"] == 2) {
            return HttpResponse::buildResponse(HttpStatusCode::CREATED, "Criado", ["id" => 2, "nome" => "Maria"]);
        }

        if ($_GET["id"] == 3) {
            return new HttpResponse(HttpStatusCode::NO_CONTENT, "Sem conteúdo");
        }

        return HttpResponse::notFound("Não encontrado");
    }

    public function recurso()
    {
        switch ($_SERVER["REQUEST_METHOD"]) {
            case "GET":
                return Request::run("index", "get_recurso");
            case "POST":
                return Request::run("index", "post_recurso");
            case "PUT":
            case "PATCH":
                return Request::run("index", "put_recurso");
            case "DELETE":
                return Request::run("index", "delete_recurso");
            case "OPTIONS":
                return Request::run("index", "options_recurso");
            default:
                return HttpError::methodNotAllowed("Method not allowed");
        }
    }

    #[Method("GET")]
    #[Cache("index-get_recurso", 10)]
    #[RequiredParams([
        "id" => ValidateField::INTEGER_IN_STRING
    ])]
    #[Details(["description" => "Listar recurso"])]
    public function get_recurso()
    {
        // Exemplo de função GET
    }

    #[Method("POST")]
    #[Cache("index-post_recurso", 10)]
    #[RequiredParams([
        "id" => ValidateField::INTEGER_IN_STRING
    ])]
    #[RequiredFields([
        "email" => ValidateField::EMAIL,
        "numero" => ValidateField::INTEGER,
    ])]
    #[Details(["description" => "Cadastrar recurso"])]
    public function post_recurso()
    {
        return HttpResponse::success("Sucesso", ["id" => 1, "nome" => "João"]);
        // Exemplo de função POST
    }

    #[Method("PUT", "PATCH")]
    #[RequiredParams([
        "id" => ValidateField::INTEGER_IN_STRING
    ])]
    #[RequiredFields([
        "email" => ValidateField::EMAIL
    ])]
    #[Details(["description" => "Atualizar recurso"])]
    public function put_recurso()
    {
        // Exemplo de função PUT
    }

    #[Method("DELETE")]
    #[RequiredParams([
        "id" => ValidateField::INTEGER_IN_STRING
    ])]
    #[Details(["description" => "Deletar recurso"])]
    public function delete_recurso()
    {
        // Exemplo de função DELETE
    }

    #[Method("OPTIONS")]
    #[Details(["description" => "Listar opções"])]
    public function options_recurso()
    {
        $controller = "index";
        return HttpResponse::returnAttributes("Recurso", [
            "Listar" => Request::getOptionsAttributes($controller, "get_recurso"),
            "Cadastrar" => Request::getOptionsAttributes($controller, "post_recurso"),
            "Atualizar" => Request::getOptionsAttributes($controller, "put_recurso"),
            "Deletar" => Request::getOptionsAttributes($controller, "delete_recurso"),
            "Listar opções" => Request::getOptionsAttributes($controller, "options_recurso"),
        ]);
    }
}
