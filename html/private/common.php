<?php

use Dotenv\Dotenv;

class Env
{
    private static $dotenv;

    public static function getValue($key)
    {
        if ((self::$dotenv instanceof Dotenv) === false) {
            self::$dotenv = Dotenv::create(__DIR__ . './conf/');
            self::$dotenv->load();
        }

        return array_key_exists($key, $_ENV) ? $_ENV[$key] : null;
    }
}
