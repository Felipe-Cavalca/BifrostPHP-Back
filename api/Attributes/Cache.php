<?php

namespace Bifrost\Attributes;

use Attribute;
use Bifrost\Core\Cache as CoreCache;
use Bifrost\Core\Get;
use Bifrost\Core\Post;
use Bifrost\Core\Session;
use Bifrost\Include\AtrributesDefaultMethods;
use Bifrost\Interface\AttributesInterface;

/**
 * Cache constructor.
 * @param string $key
 * @param int $time
 * @param array $fieldsSession
 * @return void
 */
#[Attribute]
class Cache implements AttributesInterface
{
    use AtrributesDefaultMethods;

    private string $key;
    private int $time;
    private CoreCache $cache;

    public function __construct(...$p)
    {
        $post = new Post();
        $get = new Get();
        $this->key = serialize([
            "key" => $p[0],
            "POST" => (string)$post,
            "GET" => (string)$get,
            "SESSION" => $this->getFieldsSession($p[2] ?? []),
        ]);
        $this->time = $p[1];
        $this->cache = new CoreCache();
    }

    /**
     * Valida se já existe o cache e retorna o mesmo.
     * @return mixed - Valor salvo no cache.
     */
    public function beforeRun(): mixed
    {
        if ($this->cache->exists($this->key)) {
            return $this->cache->get($this->key);
        }
        return null;
    }

    /**
     * Salva o retorno do controller no cache.
     * @param mixed $return - Retorno do controller.
     * @return void
     */
    public function afterRun(mixed $return): void
    {
        $this->cache->set($this->key, $return, $this->time);
    }

    /**
     * Retorna as opções do atributo.
     * @return array - Opções do atributo.
     */
    public function getOptions(): array
    {
        return ["Cache" => [
            "seconds" => $this->time,
        ]];
    }

    /**
     * Retorna os campos da dentro da sessão.
     * @param array $fieldsSession - array com string com os indices dos campos da sessão.
     *  - os valores são transformados em string.
     * @return string - Campos da sessão serializados.
     */
    private function getFieldsSession(array $fieldsSession): string
    {
        $session = new Session();
        $fields = [];
        foreach ($fieldsSession as $field) {
            $fields[$field] = (string) $session->$field;
        }
        return serialize($fields);
    }
}
