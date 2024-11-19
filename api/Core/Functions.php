<?php

namespace Bifrost\Core;

class Functions
{

    public static function sanitize(string $value): string
    {
        $value = str_replace("'", "''", $value);
        $value = str_replace("\\", "\\\\", $value);
        return $value;
    }

    public static function sanitizeArray(array $data): array
    {
        return array_map('addslashes', $data);
    }
}
