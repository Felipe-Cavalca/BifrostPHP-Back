<?php

namespace Bifrost\Attributes;

use Attribute;
use Bifrost\Core\Cache as CoreCache;
use Bifrost\Core\Get;
use Bifrost\Core\Post;
use Bifrost\Core\Session;
use Bifrost\Include\AtrributesDefaultMethods;
use Bifrost\Interface\AttributesInterface;

#[Attribute]
class Cache implements AttributesInterface
{
    use AtrributesDefaultMethods;

    private string $key;
    private int $time;
    private CoreCache $cache;
    private Session $session;

    public function __construct(...$p)
    {
        $post = new Post();
        $get = new Get();
        $this->session = new Session();
        $this->key = serialize([
            "key" => $p[0],
            "POST" => $post,
            "GET" => $get,
            "SESSION" => $this->getFieldsSession($p[2] ?? []),
        ]);
        $this->time = $p[1];
        $this->cache = new CoreCache();
    }

    public function beforeRun(): mixed
    {
        if ($this->cache->exists($this->key)) {
            return $this->cache->get($this->key);
        }
        return null;
    }

    public function afterRun(mixed $return): void
    {
        $this->cache->set($this->key, $return, $this->time);
    }

    public function getOptions(): array
    {
        return ["Cache" => [
            "tempo" => $this->time,
        ]];
    }

    private function getFieldsSession(array $fieldsSession): string
    {
        $fields = [];
        foreach ($fieldsSession as $field) {
            $fields[$field] = $this->session->$field;
        }
        return serialize($fields);
    }
}
