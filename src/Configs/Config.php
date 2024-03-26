<?php

namespace Ordinary9843\Configs;

use Ordinary9843\Cores\FileSystem;
use Ordinary9843\Helpers\PathHelper;
use Ordinary9843\Exceptions\InvalidException;

class Config
{
    /** @var string */
    private $binPath = '';

    /** @var string */
    private $tmpPath = '';

    /** @var FileSystem */
    private static $fileSystem = null;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->setBinPath((isset($config['binPath']) && $config['binPath']) ? $config['binPath'] : 'gs');
        $this->setTmpPath((isset($config['tmpPath']) && $config['tmpPath']) ? $config['tmpPath'] : sys_get_temp_dir());
        $this->setFileSystem((isset($config['fileSystem']) && $config['fileSystem']) ? $config['fileSystem'] : new FileSystem());
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

    /**
     * @param FileSystem $fileSystem
     * 
     * @return void
     */
    public function setFileSystem(FileSystem $fileSystem): void
    {
        self::$fileSystem = $fileSystem;
    }

    /**
     * @return FileSystem
     */
    public function getFileSystem(): FileSystem
    {
        return self::$fileSystem;
    }

    /**
     * @return void
     * 
     * @throws InvalidException
     */
    public function validateBinPath(): void
    {
        $binPath = $this->getBinPath();
        if (!$binPath || !self::$fileSystem->isValid($binPath) || !preg_match('/\d+.\d+/', shell_exec($binPath . ' --version'))) {
            throw new InvalidException('The Ghostscript binary path is not set.', InvalidException::CODE_FILEPATH, [
                'binPath' => $binPath
            ]);
        }
    }
}
