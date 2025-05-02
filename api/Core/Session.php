<?php

namespace Bifrost\Core;

class Session
{
    private static array $data = [];

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['session_instance'])) {
            $_SESSION['session_instance'] = [
                'data' => []
            ];
        }

        self::$data = &$_SESSION['session_instance']['data'];
    }

    public function __destruct()
    {
        if(empty(self::$data)) {
            $this->destroy();
        }
    }

    public function __toString(): string
    {
        return json_encode(self::$data);
    }

    public function __set($name, $value): void
    {
        self::$data[$name] = $value;
    }

    public function __get($name): mixed
    {
        return self::$data[$name] ?? null;
    }

    public function __isset($name): bool
    {
        return isset(self::$data[$name]);
    }

    public function __unset($name): void
    {
        unset(self::$data[$name]);
    }

    public function destroy(): void
    {
        session_destroy();
    }
}
