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
            self::EMAIL => self::validateEmail($val),
            default => false,
        };
    }

    private static function validateCPF($val): bool
    {
        return preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $val) === 1;
    }

    private static function validateCNPJ($val): bool
    {
        return preg_match('/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/', $val) === 1;
    }

    private static function validateEmail($val): bool
    {
        return filter_var($val, FILTER_VALIDATE_EMAIL) !== false;
    }
}
