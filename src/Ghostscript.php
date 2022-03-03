<?php

namespace Ordinary9843;

use Exception;

class Ghostscript
{
    /** @var string Ghostscript temporary prefix filename */
    const TMP_FILE_PREFIX = 'ghostscript_tmp_file_';

    /** @var string Ghostscript convert PDF command */
    protected $command = '%s -sDEVICE=pdfwrite -dCompatibilityLevel=%s -dNOPAUSE -dQUIET -dBATCH -sOutputFile=%s %s';

    /** @var string Ghostscript binary absolute path */
    protected $binPath = '';

    /** @var string Temporary save file absolute path */
    protected $tmpPath = '';

    /** @var array Ghostscript options */
    protected $options = [];

    /** @var string Error message */
    protected $error = '';

    /**
     * Initialize
     * 
     * @param string $binPath
     * @param string $tmpPath
     */
    public function __construct(string $binPath = '', string $tmpPath = '')
    {
        if ($this->getBinPath() === '') {
            if ($binPath !== '') {
                $this->setBinPath($binPath);
            }
        }

        if ($this->getTmpPath() === '') {
            if ($tmpPath !== '') {
                $this->setTmpPath($tmpPath);
            } else {
                $this->setTmpPath(sys_get_temp_dir());
            }
        }
    }

    /**
     * Convert file path separator
     * 
     * @param string $path
     * 
     * @return string
     */
    protected function convertPathSeparator(string $path): string
    {
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        return $path;
    }

    /**
     * Generate temporary file path
     * 
     * @return string
     */
    private function generateTmpFile(): string
    {
        return $this->getTmpPath() . DIRECTORY_SEPARATOR . uniqid(self::TMP_FILE_PREFIX) . '.pdf';
    }

    /**
     * Delete temporary file
     * 
     * @param bool $isForceDelete
     * @param int $days
     * 
     * @return void
     */
    public function deleteTmpFile(bool $isForceDelete = false, int $days = 7): void
    {
        $deleteSeconds = $days * 86400;
        $tmpPath = $this->getTmpPath();
        $files = scandir($tmpPath);

        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $path = $tmpPath . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
                $createdAt = filemtime($path);
                $isExpired = time() - $createdAt > $deleteSeconds;

                if ($isForceDelete === true || $isExpired === true) {
                    $pathInfo = pathinfo($path);
                    $filename = $pathInfo['filename'];

                    if (preg_match('/' . self::TMP_FILE_PREFIX . '/', $filename)) {
                        unlink($path);
                    }
                }
            }
        }
    }

    /**
     * Get temporary file count
     * 
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
            if (is_file($path)) {
                $pathInfo = pathinfo($path);
                $filename = $pathInfo['filename'];

                (preg_match('/' . self::TMP_FILE_PREFIX . '/', $filename)) && $count++;
            }
        }

        return $count;
    }

    /**
     * Validate ghostscript binary path
     * 
     * @return void
     * 
     * @throws Exception
     */
    private function validateBinPath(): void
    {
        $binPath = $this->getBinPath();
        if (!is_dir($binPath) && !is_file($binPath)) {
            throw new Exception('The ghostscript binary path is not set.');
        }
    }

    /**
     * Set ghostscript binary absolute path
     * 
     * @param string $binPath
     * 
     * @return void
     */
    public function setBinPath(string $binPath): void
    {
        $binPath = $this->convertPathSeparator($binPath);

        $this->binPath = $binPath;
    }

    /**
     * Get ghostscript binary absolute path
     * 
     * @return string
     */
    public function getBinPath(): string
    {
        return $this->binPath;
    }

    /**
     * Set temporary save file absolute path
     * 
     * @param string $tmpPath
     * 
     * @return void
     */
    public function setTmpPath(string $tmpPath): void
    {
        $tmpPath = $this->convertPathSeparator($tmpPath);

        $this->tmpPath = $tmpPath;
    }

    /**
     * Get temporary save file absolute path
     * 
     * @return string
     */
    public function getTmpPath(): string
    {
        return $this->tmpPath;
    }

    /**
     * Get execute Ghostscript options
     * 
     * @param array $options
     * 
     * @return void
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * Get execute Ghostscript options
     * 
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Get error message
     * 
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Set error message
     * 
     * @return void
     */
    protected function setError(string $error): void
    {
        $this->error = $error;
    }

    /**
     * Compose execution command
     * 
     * @param float $version
     * @param string $tmpFile
     * @param string $file
     * 
     * @return string
     */
    private function command(float $version, string $tmpFile, string $file): string
    {
        $command = sprintf($this->command, $this->binPath, $version, $tmpFile, escapeshellarg($file));
        $options = $this->getOptions();
        if (!empty($options)) {
            foreach ($options as $key => $value) {
                if (!is_numeric($key)) {
                    $command .= ' ' . $key . '=' . $value;
                } else {
                    $command .= ' ' . $value;
                }
            }
        }

        return $command;
    }

    /**
     * Guess PDF version
     * 
     * @param string $file
     * 
     * @return float
     */
    public function guess(string $file): float
    {
        $version = 0;

        if (!is_file($file)) {
            $this->setError($file . ' not exists.');

            return $version;
        }

        $fo = @fopen($file, 'rb');
        fseek($fo, 0);
        preg_match('/%PDF-(\d\.\d)/', fread($fo, 1024), $match);
        fclose($fo);

        $version = (float)($match[1] ?? $version);

        return $version;
    }

    /**
     * Convert PDF version
     * 
     * @param string $file
     * @param float $newVersion
     * 
     * @return string
     * 
     * @throws Exception
     */
    public function convert(string $file, float $newVersion): string
    {
        $this->validateBinPath();

        $file = $this->convertPathSeparator($file);
        if (!is_file($file)) {
            $this->setError('Failed to convert, ' . $file . ' not exists.');

            return $file;
        }

        $tmpFile = $this->generateTmpFile();
        $command = $this->command($newVersion, $tmpFile, $file);
        $output = shell_exec($command);
        if ($output) {
            $this->setError('Failed to convert ' . $file . '. Because ' . $output);

            return $file;
        }

        copy($tmpFile, $file);

        return $file;
    }
}