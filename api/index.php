<?php

namespace Bifrost;

require_once __DIR__ . "/Core/Autoload.php";

use Bifrost\Core\Request;

/**
 * Este arquivo é o ponto de entrada para o servidor.
 */
print new Request();
