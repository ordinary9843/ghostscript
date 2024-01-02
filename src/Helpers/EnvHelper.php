<?php

namespace Ordinary9843\Helpers;

use Dotenv\Dotenv;

class EnvHelper
{
    /** @var Dotenv */
    private static $dotenv;

    /**
     * @param string $key
     * @param string $default
     *
     * @return string|bool|int|float|null
     */
    public static function get(string $key, string $default = '')
    {
        if (self::$dotenv === null) {
            self::$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
            self::$dotenv->safeLoad();
        }

        return $_ENV[$key] ?? $default;
    }
}
