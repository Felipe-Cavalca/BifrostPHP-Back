<?php

namespace Bifrost\Enum;

enum Field: string
{
    case INTEGER = 'Número inteiro';
    case INTEGER_IN_STRING = 'Número inteiro em string';
    case STRING = 'Texto';
    case FLOAT = 'Número decimal';
    case BOOLEAN = 'Booleano';
    case ARRAY = 'Array';
    case OBJECT = 'Objeto';
    case NULL = 'Nulo';
    case DEFAULT = 'Tipo não identificado';
    case CPF = 'Cpf';
    case CNPJ = 'Cnpj';
    case EMAIL = 'Email';
    case URL = 'Url';
    case BASE64 = 'Base64';
    case FILE_PATH = 'Caminho de arquivo';

    public function validate($val): bool
    {
        return match ($this) {
            self::INTEGER => is_int($val),
            self::INTEGER_IN_STRING => ctype_digit($val),
            self::STRING => is_string($val),
            self::FLOAT => is_float($val),
            self::BOOLEAN => is_bool($val),
            self::ARRAY => is_array($val),
            self::OBJECT => is_object($val),
            self::NULL => is_null($val),
            self::CPF => self::validateCPF($val),
            self::CNPJ => self::validateCNPJ($val),
            self::EMAIL => filter_var($val, FILTER_VALIDATE_EMAIL) !== false,
            self::URL => filter_var($val, FILTER_VALIDATE_URL) !== false,
            self::BASE64 => is_string($val) && base64_decode($val, true) !== false,
            self::FILE_PATH => is_string($val),
            default => false,
        };
    }

    private static function validateCPF($val): bool
    {
        $val = preg_replace('/\D/', '', $val);

        if (strlen($val) != 11) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $val[$i] * (10 - $i);
        }
        $firstDigit = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);
        if ($val[9] != $firstDigit) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $val[$i] * (11 - $i);
        }
        $secondDigit = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);
        if ($val[10] != $secondDigit) {
            return false;
        }

        return true;
    }

    private static function validateCNPJ($val): bool
    {
        $val = preg_replace('/\D/', '', $val);

        if (strlen($val) != 14) {
            return false;
        }

        $sum = 0;
        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 12; $i++) {
            $sum += $val[$i] * $weights[$i];
        }
        $firstDigit = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);
        if ($val[12] != $firstDigit) {
            return false;
        }

        $sum = 0;
        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 13; $i++) {
            $sum += $val[$i] * $weights[$i];
        }
        $secondDigit = ($sum % 11 < 2) ? 0 : 11 - ($sum % 11);
        if ($val[13] != $secondDigit) {
            return false;
        }

        return true;
    }
}
