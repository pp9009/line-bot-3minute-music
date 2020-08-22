<?php

class Conf
{

    private static $ini_file = 'conf.ini';
    private static $value = null;

    public static function init()
    {
        self::$value = parse_ini_file(__DIR__ . '/conf/' . self::$ini_file, true);
    }

    public static function getValue($section, $key)
    {
        return self::$value[$section][$key];
    }
}

Conf::init();