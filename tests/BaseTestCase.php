<?php

namespace Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    /** @var Dotenv */
    private static $dotenv;

    /**
     * @param string $key
     * @param string $default
     *
     * @return string|bool|int|float|null
     */
    protected function getEnv(string $key, string $default = '')
    {
        if (self::$dotenv === null) {
            self::$dotenv = Dotenv::createImmutable(dirname(__DIR__, 1));
            self::$dotenv->safeLoad();
        }

        return $_ENV[$key] ?? $default;
    }
}
