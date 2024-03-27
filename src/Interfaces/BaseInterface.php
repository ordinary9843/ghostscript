<?php

namespace Ordinary9843\Interfaces;

interface BaseInterface
{
    /**
     * @param string $binPath
     * 
     * @return void
     */
    public function setBinPath(string $binPath): void;

    /**
     * @return string
     */
    public function getBinPath(): string;

    /**
     * @param string $tmpPath
     * 
     * @return void
     */
    public function setTmpPath(string $tmpPath): void;

    /**
     * @return string
     */
    public function getTmpPath(): string;
}
