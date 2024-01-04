<?php

namespace Tests;

use Dotenv\Dotenv;
use PHPUnit\Runner\Version;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /** @var string */
    const PHPUNIT_MIN_VERSION = '0.0.0';

    /** @var string */
    const PHPUNIT_VERSION_9 = '9.0.0';

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

    /**
     * @param string $minVersion
     * @param string $maxVersion
     *
     * @return bool
     */
    protected function isPhpUnitVersionInRange(string $minVersion, string $maxVersion): bool
    {
        $currentVersion = Version::id();

        return version_compare($currentVersion, $minVersion, '>=') && version_compare($currentVersion, $maxVersion, '<');
    }
}
