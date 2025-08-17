<?php

namespace Bifrost\Core;

class Functions
{

    public static function sanitize(mixed $value): string
    {
        if (is_string($value)) {
            $value = addslashes($value);
        }
        return $value;
    }

    public static function sanitizeArray(array $data): array
    {
        return array_map(function ($item) {
            if (is_string($item)) {
                return addslashes($item);
            }
            return $item; // mantém números e outros tipos intactos
        }, $data);
    }
}
