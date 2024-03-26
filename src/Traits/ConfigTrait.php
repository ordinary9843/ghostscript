<?php

namespace Ordinary9843\Traits;

use Ordinary9843\Configs\Config;

trait ConfigTrait
{
    /**
     * @param string $binPath
     * 
     * @return void
     */
    public function setBinPath(string $binPath): void
    {
        Config::getInstance()->setBinPath($binPath);
    }

    /**
     * @return string
     */
    public function getBinPath(): string
    {
        return Config::getInstance()->getBinPath();
    }

    /**
     * @param int $tmpPath
     * 
     * @return void
     */
    public function setTmpPath(string $tmpPath): void
    {
        Config::getInstance()->setTmpPath($tmpPath);
    }

    /**
     * @return string
     */
    public function getTmpPath(): string
    {
        return Config::getInstance()->getTmpPath();
    }
}
