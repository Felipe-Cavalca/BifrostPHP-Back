<?php

namespace Bifrost\Attributes;

use Attribute;
use Bifrost\Class\HttpResponse;
use Bifrost\Core\Post;
use Bifrost\Enum\Field;
use Bifrost\Include\AtrributesDefaultMethods;
use Bifrost\Interface\AttributesInterface;

#[Attribute]
class OptionalFields implements AttributesInterface
{

    use AtrributesDefaultMethods;

    public static array $fields = [];
    private array $errors = [];

    /**
     * @param array $params - Campos opcionais a serem mostrados.
     */
    public function __construct(...$fields)
    {
        self::$fields = $fields[0];
    }

    /**
     * Valida os campos opcionais e retorna erro caso algum campo não seja válido.
     * @return mixed - Erro de requisição ou null caso não tenha erro.
     */
    public function beforeRun(): mixed
    {
        if (!$this->validateOptionalFields(self::$fields)) {
            return HttpResponse::badRequest(
                errors: $this->getErrors(),
                message: "Invalid optional fields",
            );
        }
        return null;
    }

    public function getOptions(): array
    {
        $campos = [];
        foreach (self::$fields as $field => $filter) {
            $campos[$field] = $filter->value ?? null;
        }
        return ["optionalFields" => $campos];
    }

    /**
     * Valida os campos opcionais e retorna erro caso algum campo não seja válido.
     * @param array $fields - Campos opcionais para o endpoint.
     * @return bool - True caso todos os campos sejam válidos, false caso contrário.
     */
    public function validateOptionalFields(array $fields): bool
    {
        $this->errors = [];

        foreach ($fields as $field => $filter) {
            if (is_int($field)) {
                $field = $filter;
                $filter = Field::DEFAULT;
            }

            if (!static::validateType($field, $filter) && empty($this->errors[$field])) {
                $this->errors[$field] = "Invalid field type";
            }
        }

        if (empty($this->errors)) {
            return true;
        }

        return false;
    }

    /**
     * Retorna os erros encontrados na validação dos campos opcionais.
     * @return array - Erros encontrados na validação dos campos opcionais.
     */
    private function getErrors(): array
    {
        return ["fields" => $this->errors];
    }

    /**
     * Valida o tipo do campo.
     * @param string $field - Campo a ser validado.
     * @param Field $filter - Tipo do campo a ser validado.
     * @return bool - True caso o tipo do campo seja válido, false caso contrário.
     */
    private function validateType(string $field, Field $filter): bool
    {
        $post = new Post();
        if (!isset($post->$field)) {
            return true;
        }
        return $filter->validate($post->$field);
    }
}
