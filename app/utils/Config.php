<?php

namespace App\Utils;

class Config
{
    private static $config;

    public static function load($file = __DIR__ . '/../../config/config.php')
    {
        if (file_exists($file)) {
            self::$config = include $file;
        } else {
            throw new \Exception("Config file not found: " . $file);
        }
    }

    public static function get($key, $default = null)
    {
        return self::$config[$key] ?? $default;
    }
}
