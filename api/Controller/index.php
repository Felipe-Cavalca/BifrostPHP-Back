<?php

namespace Bifrost\Controller;

use Bifrost\Interface\ControllerInterface;
use Bifrost\Include\Controller;
use Bifrost\Attributes\Method;
use Bifrost\Attributes\RequiredFields;
use Bifrost\Attributes\RequiredParams;
use Bifrost\Attributes\Cache;
use Bifrost\Core\Settings;
use Bifrost\Enum\HttpStatusCode;
use Bifrost\Class\HttpResponse;
use Bifrost\Class\HttpError;

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

        if($_GET["id"] == 3) {
            return new HttpResponse(HttpStatusCode::NO_CONTENT, "Sem conteúdo");
        }

        return HttpResponse::notFound("Não encontrado");
    }

    #[Method(["POST"])]
    #[RequiredFields([
        "email" => FILTER_VALIDATE_EMAIL,
        "numero" => FILTER_VALIDATE_INT,
    ])]
    #[RequiredParams(["id"])]
    #[Cache("index-data", 10)]
    public function data()
    {
        return [
            "id" => $_GET["id"],
            "email" => $_POST["email"],
            "numero" => $_POST["numero"],
        ];
    }
}
