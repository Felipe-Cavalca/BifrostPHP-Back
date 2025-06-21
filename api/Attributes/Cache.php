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
 *
 * @param int $time Tempo em segundos que o cache ficará salvo.
 * @param array $fieldsSession Campos da sessão que serão levados em conta ao gerar a chave.
 * @param array $extra Array de valores extras utilizados para compor a chave.
 * @return void
 */
#[Attribute]
class Cache implements AttributesInterface
{
    use AtrributesDefaultMethods;

    private string $key;
    private int $time;
    private CoreCache $cache;

    public function __construct(...$parms)
    {
        $post = new Post();
        $get = new Get();

        $dataKey = [
            "METHOD" => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            "POST" => (string) $post,
            "GET" => (string) $get,
            "SESSION" => $this->getFieldsSession($parms[1] ?? []),
        ];

        if (!empty($parms[2]) && is_array($parms[2])) {
            $dataKey["EXTRA"] = serialize($parms[2]);
        }

        $this->key = serialize($dataKey);
        $this->time = $parms[0];
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
        return ["cache" => [
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
