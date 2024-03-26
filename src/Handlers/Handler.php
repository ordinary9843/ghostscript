<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Configs\Config;
use Ordinary9843\Cores\FileSystem;
use Ordinary9843\Traits\MessageTrait;
use Ordinary9843\Exceptions\BaseException;
use Ordinary9843\Constants\MessageConstant;
use Ordinary9843\Exceptions\HandlerException;
use Ordinary9843\Exceptions\InvalidException;
use Ordinary9843\Interfaces\HandlerInterface;

class Handler implements HandlerInterface
{
    use MessageTrait;

    /** @var string */
    const TMP_FILE_PREFIX = 'ghostscript_tmp_file_';

    /** @var Config */
    private static $config = null;

    /** @var array */
    private $options = [];

    /** @var array */
    private $tmpFiles = [];

    /**
     * @param Config $config
     */
    public function __construct(Config $config = null)
    {
        self::$config = ($config !== null) ? $config : new Config();
    }

    /**
     * @param Config $config
     *
     * @return void
     */
    public function setConfig(Config $config): void
    {
        self::$config = $config;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return self::$config;
    }

    /**
     * @param FileSystem $fileSystem
     * 
     * @return void
     */
    public function setFileSystem(FileSystem $fileSystem): void
    {
        $this->getConfig()->setFileSystem($fileSystem);
    }

    /**
     * @return FileSystem
     */
    public function getFileSystem(): FileSystem
    {
        return $this->getConfig()->getFileSystem();
    }

    /**
     * @param string $binPath
     * 
     * @return void
     */
    public function setBinPath(string $binPath): void
    {
        $this->getConfig()->setBinPath($binPath);
    }

    /**
     * @return string
     */
    public function getBinPath(): string
    {
        return $this->getConfig()->getBinPath();
    }

    /**
     * @param string $tmpPath
     * 
     * @return void
     */
    public function setTmpPath(string $tmpPath): void
    {
        $this->getConfig()->setTmpPath($tmpPath);
    }

    /**
     * @return string
     */
    public function getTmpPath(): string
    {
        return $this->getConfig()->getTmpPath();
    }

    /**
     * @return void
     * 
     * @throws InvalidException
     */
    public function validateBinPath(): void
    {
        $this->getConfig()->validateBinPath();
    }

    /**
     * @param array $options
     *
     * @return void
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return array
     */
    protected function getTmpFiles(): array
    {
        return $this->tmpFiles;
    }

    /**
     * @param string $filename
     * 
     * @return string
     */
    public function getTmpFile(string $filename = ''): string
    {
        return $this->getTmpPath() . DIRECTORY_SEPARATOR . uniqid(self::TMP_FILE_PREFIX . $filename) . '.pdf';
    }

    /**
     * @return int
     */
    public function getTmpFileCount(): int
    {
        $tmpPath = $this->getTmpPath();
        $files = scandir($tmpPath);
        $count = 0;
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $path = $tmpPath . DIRECTORY_SEPARATOR . $file;
            if ($this->getFileSystem()->isFile($path)) {
                $pathInfo = pathinfo($path);
                $filename = $pathInfo['filename'];
                (preg_match('/' . self::TMP_FILE_PREFIX . '/', $filename)) && $count++;
            }
        }

        return $count;
    }

    /**
     * @param bool $isForceClear
     * @param int $days
     *
     * @return void
     */
    public function clearTmpFiles(bool $isForceClear = false, int $days = 7): void
    {
        $deleteSeconds = $days * 86400;
        $fileSystem = $this->getFileSystem();
        $tmpPath = $this->getTmpPath();
        $files = scandir($tmpPath);
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $path = $tmpPath . DIRECTORY_SEPARATOR . $file;
            if ($fileSystem->isFile($path)) {
                $createdAt = filemtime($path);
                $isExpired = time() - $createdAt > $deleteSeconds;
                if ($isForceClear === true || $isExpired === true) {
                    $pathInfo = pathinfo($path);
                    $filename = $pathInfo['filename'];
                    (preg_match('/' . self::TMP_FILE_PREFIX . '/', $filename)) && $fileSystem->delete($path);
                }
            }
        }

        foreach ($this->getTmpFiles() as $file) {
            $fileSystem->delete($file);
        }
    }

    /**
     * @param string $command
     *
     * @return string
     */
    public function optionsToCommand(string $command): string
    {
        $options = $this->getOptions();

        return (!empty($options)) ? $command .= ' ' . implode(' ', array_map(function ($key, $value) {
            return is_numeric($key) ? $value : $key . '=' . $value;
        }, array_keys($options), $options)) : $command;
    }

    /**
     * @param string $file
     *
     * @return int
     * 
     * @throws InvalidException
     */
    public function getPdfTotalPage(string $file): int
    {
        try {
            $this->validateBinPath();

            if (!$this->getFileSystem()->isFile($file)) {
                throw new InvalidException('"' . $file . '" is not exist.', InvalidException::CODE_FILEPATH);
            } elseif (!$this->isPdf($file)) {
                throw new InvalidException('"' . $file . '" is not PDF.', InvalidException::CODE_FILE_TYPE);
            }

            $output = shell_exec(
                sprintf(
                    '%s -dQUIET -dNODISPLAY -dNOSAFER -c "(%s) (r) file runpdfbegin pdfpagecount = quit"',
                    $this->getBinPath(),
                    $file
                )
            );

            return ($output) ? (int)$output : 0;
        } catch (BaseException $e) {
            $this->addMessage(MessageConstant::TYPE_ERROR, $e->getMessage());

            return 0;
        }
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    public function isPdf(string $file): bool
    {
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'pdf') {
            return false;
        }

        return (mime_content_type($file) === 'application/pdf');
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function convertToTmpFile(string $file): string
    {
        $tmpFile = $this->getTmpFile(md5($file));
        @copy($file, $tmpFile);
        $this->addTmpFile($tmpFile);

        return $tmpFile;
    }

    /**
     * @param string $file
     * 
     * @return void
     */
    protected function addTmpFile(string $file): void
    {
        $this->tmpFiles[] = $file;
    }

    /**
     * @param array ...$arguments
     * 
     * @return mixed
     * 
     * @throws HandlerException
     */
    public function execute(...$arguments)
    {
        throw new HandlerException('The method has not implemented yet.', HandlerException::CODE_EXECUTE);
    }
}
