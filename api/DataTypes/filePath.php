<?php

namespace Bifrost\DataTypes;

use Bifrost\Enum\Field;
use Bifrost\Include\AbstractFieldValue;

class FilePath
{
    use AbstractFieldValue;

    public function __construct(mixed $filePath)
    {
        $this->init($filePath, Field::FILE_PATH);
    }
}
