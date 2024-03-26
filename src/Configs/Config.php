<?php

namespace Ordinary9843\Configs;

use Ordinary9843\Helpers\PathHelper;
use Ordinary9843\Exceptions\ConfigException;

class Config
{
    /** @var Config */
    private static $instance = null;

    /** @var string */
    protected $binPath = 'gs';

    /** @var string */
    protected $tmpPath = '/tmp';

    /**
     * @throws ConfigException
     */
    public function __clone()
    {
        throw new ConfigException('Cannot clone a singleton instance.', ConfigException::CODE_CLONE);
    }

    /**
     * @throws ConfigException
     */
    public function __wakeup()
    {
        throw new ConfigException('Cannot unserialize a singleton instance.', ConfigException::CODE_WAKEUP);
    }

    /**
     * @param array $arguments
     * 
     * @return void
     */
    public static function initialize(array $arguments = []): void
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        (isset($arguments['binPath'])) && self::$instance->setBinPath($arguments['binPath']);
        (isset($arguments['tmpPath'])) && self::$instance->setTmpPath($arguments['tmpPath']);
    }

    /**
     * @param array $arguments
     * 
     * @return Config
     */
    public static function getInstance(array $arguments = []): Config
    {
        self::initialize($arguments);

        return self::$instance;
    }

    /**
     * @param string $binPath
     * 
     * @return void
     */
    public function setBinPath(string $binPath): void
    {
        $this->binPath = PathHelper::convertPathSeparator($binPath);
    }

    /**
     * @return string
     */
    public function getBinPath(): string
    {
        return $this->binPath;
    }

    /**
     * @param string $tmpPath
     * 
     * @return void
     */
    public function setTmpPath(string $tmpPath): void
    {
        $this->tmpPath = PathHelper::convertPathSeparator($tmpPath);
    }

    /**
     * @return string
     */
    public function getTmpPath(): string
    {
        return $this->tmpPath;
    }
}
