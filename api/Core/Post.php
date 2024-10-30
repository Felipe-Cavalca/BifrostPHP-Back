<?php

namespace Bifrost\Core;

class Post
{
    private static $data;

    public function __construct()
    {
        if (! $_POST instanceof Post) {
            $_POST = json_decode(file_get_contents('php://input'));
            self::$data = $_POST;
            $_POST = $this;
        }
    }

    public function __toString()
    {
        return json_encode(self::$data);
    }

    public function __get($name)
    {
        return self::$data->$name ?? null;
    }

    public function __isset($name)
    {
        return isset(self::$data->$name);
    }

    public function __unset($name)
    {
        unset(self::$data->$name);
    }
}
