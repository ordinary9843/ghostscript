<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Configs\Config;
use Ordinary9843\Traits\ConfigTrait;
use Ordinary9843\Traits\FileSystemTrait;
use Ordinary9843\Exceptions\HandlerException;
use Ordinary9843\Exceptions\InvalidException;
use Ordinary9843\Interfaces\HandlerInterface;

class BaseHandler implements HandlerInterface
{
    use ConfigTrait, FileSystemTrait;

    /** @var string */
    const TMP_FILE_PREFIX = 'ghostscript_tmp_file_';

    /** @var Config */
    protected $config = null;

    /** @var array */
    protected $argumentsMapping = [];

    /** @var array */
    protected $options = [];

    /** @var array */
    protected $tmpFiles = [];

    public function __construct()
    {
        $this->config = Config::getInstance();
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
            if ($this->isFile($path)) {
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
        $tmpPath = $this->getTmpPath();
        $files = scandir($tmpPath);
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $path = $tmpPath . DIRECTORY_SEPARATOR . $file;
            if ($this->isFile($path)) {
                $createdAt = filemtime($path);
                $isExpired = time() - $createdAt > $deleteSeconds;
                if ($isForceClear === true || $isExpired === true) {
                    $pathInfo = pathinfo($path);
                    $filename = $pathInfo['filename'];
                    (preg_match('/' . self::TMP_FILE_PREFIX . '/', $filename)) && $this->delete($path);
                }
            }
        }

        foreach ($this->getTmpFiles() as $file) {
            $this->delete($file);
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
     * @return void
     * 
     * @throws InvalidException
     */
    public function validateBinPath(): void
    {
        $binPath = $this->getBinPath();
        if (!$binPath || !$this->isValid($binPath) || !preg_match('/\d+.\d+/', shell_exec($binPath . ' --version'))) {
            throw new InvalidException('The Ghostscript binary path is not set.', InvalidException::CODE_FILEPATH, [
                'binPath' => $binPath
            ]);
        }
    }

    /**
     * @return array
     */
    protected function getTmpFiles(): array
    {
        return $this->tmpFiles;
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
     * @param array $arguments
     * 
     * @return void
     */
    protected function mapArguments(array &$arguments): void
    {
        if (!empty($this->argumentsMapping)) {
            $arguments += array_fill(0, count($this->argumentsMapping), null);
            $arguments = array_combine($this->argumentsMapping, $arguments);
        }
    }
}
