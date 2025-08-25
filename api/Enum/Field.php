<?php

namespace Bifrost\Enum;

/**
 * Enum para representar os tipos de dados suportados.
 */
enum Field: string
{
    case INT = 'Integer';
    case INT_IN_STRING = 'Integer in string';
    case STRING = 'String';
    case FLOAT = 'Float';
    case BOOL = 'Boolean';
    case ARRAY = 'Array';
    case OBJECT = 'Object';
    case NULL = 'Null';
    case DEFAULT = 'Unidentified type';
    case CPF = 'Cpf';
    case CNPJ = 'Cnpj';
    case EMAIL = 'Email';
    case URL = 'Url';
    case BASE64 = 'Base64';
    case JSON = 'JSON';
    case UUID = 'UUID';
    case FOLDER_NAME = 'Folder name';
    case FOLDER_PATH = 'Folder path';
    case FILE_NAME = 'File name';
    case FILE_PATH = 'File path';

    public function validate($val): bool
    {
        return match ($this) {
            self::INT => is_int($val),
            self::INT_IN_STRING => ctype_digit($val),
            self::STRING => is_string($val),
            self::FLOAT => is_float($val),
            self::BOOL => is_bool($val),
            self::ARRAY => is_array($val),
            self::OBJECT => is_object($val),
            self::NULL => is_null($val),
            self::CPF => self::validateCPF($val),
            self::CNPJ => self::validateCNPJ($val),
            self::EMAIL => filter_var($val, FILTER_VALIDATE_EMAIL) !== false,
            self::URL => filter_var($val, FILTER_VALIDATE_URL) !== false,
            self::BASE64 => is_string($val) && base64_decode($val, true) !== false,
            self::JSON => json_decode($val) !== null,
            self::UUID => self::validateUUID($val),
            self::FOLDER_NAME => self::validateFolderName($val),
            self::FOLDER_PATH => self::validateFolderPath($val),
            self::FILE_NAME => self::validateFileName($val),
            self::FILE_PATH => self::validateFilePath($val),
            default => false,
        };
    }

    /**
     * Valida CPF.
     * @param string $val CPF a ser validado.
     * @return bool Retorna true se o CPF for válido, caso contrário, false.
     */
    public static function validateCPF($val): bool
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

    /**
     * Valida CNPJ.
     * @param string $val CNPJ a ser validado.
     * @return bool Retorna true se o CNPJ for válido, caso contrário, false.
     */
    public static function validateCNPJ($val): bool
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

    public static function validateUUID($val = null): bool
    {
        if (empty($val)) {
            return false;
        }
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $val) === 1;
    }

    /**
     * Valida o nome de uma pasta.
     * @param string $val Nome da pasta a ser validado.
     * @return bool Retorna true se o nome for válido, caso contrário, false.
     */
    public static function validateFolderName(string $val): bool
    {
        if (!is_string($val)) {
            return false;
        }

        // Permite apenas caracteres válidos para pastas e tamanho entre 1 e 255
        return preg_match('/^(?!.*\.)[^\\/:*?"<>|]{1,255}$/', $val) === 1;
    }

    /**
     * Valida o caminho de uma pasta.
     * @param string $val Caminho da pasta a ser validado.
     * @return bool Retorna true se o caminho for válido, caso contrário, false.
     */
    public static function validateFolderPath(string $val): bool
    {
        if (!is_string($val)) {
            return false;
        }

        foreach (explode('/', $val) as $key => $folder) {
            // !== por conta de path que começa com "/", então no explode vem uma string vazia
            if ($key === 0 && $folder === '') {
                // Se for o primeiro elemento e for vazio, é um caminho absoluto, então não valida
                continue;
            }
            if (!self::validateFolderName($folder)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Valida o nome de um arquivo.
     * @param string $val Nome do arquivo a ser validado.
     * @return bool Retorna true se o nome for válido, caso contrário, false.
     */
    public static function validateFileName(string $val): bool
    {
        if (!is_string($val)) {
            return false;
        }

        if (strpos($val, '/') !== false || strpos($val, '\\') !== false) {
            return false;
        }
        if ($val === '' || strlen($val) > 255) {
            return false;
        }
        // Não permite "." ou ".."
        if ($val === '.' || $val === '..') {
            return false;
        }
        // Não permite nomes começando ou terminando com ponto
        if ($val[0] === '.' || substr($val, -1) === '.') {
            return false;
        }
        // Não permite caracteres inválidos
        return preg_match('/^[^\\/:*?"<>|]+$/', $val) === 1;
    }

    /**
     * Valida o caminho de um arquivo.
     * @param string $val Caminho do arquivo a ser validado.
     * @return bool Retorna true se o caminho for válido, caso contrário, false.
     */
    public static function validateFilePath(string $val): bool
    {
        if (!is_string($val)) {
            return false;
        }

        $parts = explode('/', $val);
        if (count($parts) < 1) {
            return false;
        }

        // Todas as partes, exceto a última, devem ser nomes de pastas válidos
        for ($i = 0; $i < count($parts) - 1; $i++) {
            if (!self::validateFolderName($parts[$i])) {
                return false;
            }
        }

        // A última parte deve ser um nome de arquivo válido
        return self::validateFileName(end($parts));
    }
}
