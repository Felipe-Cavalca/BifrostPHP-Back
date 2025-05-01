<?php

namespace Bifrost\Enum;

/**
 * Enum para representar os caminhos de classes no Bifrost.
 */
enum Path: string
{
    case CLASSE = "Bifrost\\Class\\";
    case CONTROLLERS = "Bifrost\\Controller\\";
    case MODEL = "Bifrost\\Model\\";
}
