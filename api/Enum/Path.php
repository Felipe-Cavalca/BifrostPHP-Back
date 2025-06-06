<?php

namespace Bifrost\Enum;

/**
 * Enum para representar os caminhos de classes no Bifrost.
 */
enum Path: string
{
    case FOLDER = ".\\";
    case NAMESPACE = "Bifrost\\";
    case CLASSE = "Class\\";
    case CONTROLLERS = "Controller\\";
    case MODEL = "Model\\";

    /**
     * Troca o separador de namespace para o separador de diretório.
     */
    public function toDirectory(): string
    {
        return str_replace("\\", DIRECTORY_SEPARATOR, $this->value);
    }
}
