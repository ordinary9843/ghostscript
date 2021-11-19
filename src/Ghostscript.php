<?php

namespace Ordinary9843;

use Exception;

class Ghostscript
{
    /** @var string Ghostscript convert PDF base command */
    const BASE_COMMAND = '%s -sDEVICE=pdfwrite -dCompatibilityLevel=%s -dPDFSETTINGS=/screen -dNOPAUSE -dQUIET -dBATCH -dColorConversionStrategy=/LeaveColorUnchanged -dEncodeColorImages=false -dEncodeGrayImages=false -dEncodeMonoImages=false -dDownsampleMonoImages=false -dDownsampleGrayImages=false -dDownsampleColorImages=false -dAutoFilterColorImages=false -dAutoFilterGrayImages=false -dColorImageFilter=/FlateEncode -dGrayImageFilter=/FlateEncode -sOutputFile=%s %s';

    /** @var string Ghostscript temporary prefix filename */
    const TMP_FILE_PREFIX = 'ghostscript_tmp_file_';

    /** @var string Ghostscript binary absolute path */
    private $binPath;

    /** @var string Temporary save file absolute path */
    private $tmpPath;

    /**
     * Initialize
     * 
     * @param string|null $binPath
     * @param string|null $tmpPath
     * 
     * @return void
     */
    public function __construct(string $binPath = null, string $tmpPath = null)
    {
        if ($this->binPath === null) {
            if ($binPath !== null) {
                $this->setBinPath($binPath);
            }
        }

        if ($this->tmpPath === null) {
            if ($tmpPath !== null) {
                $this->setTmpPath($tmpPath);
            } else {
                $this->tmpPath = sys_get_temp_dir();
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
    protected function convertPathSeparator(string $path)
    {
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        return $path;
    }

    /**
     * Generate temporary file path
     * 
     * @return string
     */
    private function generateTmpFile()
    {
        return $this->tmpPath . DIRECTORY_SEPARATOR . uniqid(self::TMP_FILE_PREFIX) . '.pdf';
    }

    /**
     * Delete temporary file
     * 
     * @param bool $isForceDelete
     * @param int $days
     * 
     * @return void
     */
    public function deleteTmpFile(bool $isForceDelete = false, int $days = 7)
    {
        $deleteSeconds = $days * 86400;
        $files = scandir($this->tmpPath);

        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $path = $this->tmpPath . DIRECTORY_SEPARATOR . $file;

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
    public function getTmpFileCount()
    {
        $files = scandir($this->tmpPath);

        $count = 0;
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $path = $this->tmpPath . DIRECTORY_SEPARATOR . $file;

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
    private function validateBinPath()
    {
        if (!is_dir($this->binPath) && !is_file($this->binPath)) {
            $this->throwException('The ghostscript binary path is not set.');
        }
    }

    /**
     * Throw exception message
     * 
     * @param string $message
     * 
     * @return void
     * 
     * @throws Exception
     */
    private function throwException(string $message)
    {
        throw new Exception($message);
    }

    /**
     * Set ghostscript binary absolute path
     * 
     * @param string $binPath
     * 
     * @return void
     */
    public function setBinPath(string $binPath)
    {
        $binPath = $this->convertPathSeparator($binPath);

        $this->binPath = $binPath;
    }

    /**
     * Get ghostscript binary absolute path
     * 
     * @return string
     */
    public function getBinPath()
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
    public function setTmpPath(string $tmpPath)
    {
        $tmpPath = $this->convertPathSeparator($tmpPath);

        $this->tmpPath = $tmpPath;
    }

    /**
     * Get temporary save file absolute path
     * 
     * @return string
     */
    public function getTmpPath()
    {
        return $this->tmpPath;
    }

    /**
     * Guess PDF version
     * 
     * @param string $file
     * 
     * @return float
     * 
     * @throws Exception
     */
    public function guess(string $file)
    {
        $version = 0;

        if (!is_file($file)) {
            $this->throwException($file . ' not exists.');
        }

        $fo = fopen($file, 'rb');

        if (!$fo) {
            $this->throwException($file . ' file can not open.');
        }

        fseek($fo, 0);
        preg_match('/%PDF-(\d\.\d)/', fread($fo, 1024), $match);
        fclose($fo);

        $version = $match[1] ?? $version;

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
    public function convert(string $file, float $newVersion)
    {
        $this->validateBinPath();

        $file = $this->convertPathSeparator($file);

        if (!is_file($file)) {
            $this->throwException('Failed to convert, ' . $file . ' not exists.');
        }

        $tmpFile = $this->generateTmpFile();
        $command = sprintf(self::BASE_COMMAND, $this->binPath, $newVersion, $tmpFile, escapeshellarg($file));
        $output = shell_exec($command);

        if ($output) {
            $this->throwException('Failed to convert ' . escapeshellarg($file) . '. Because ' . $output);
        }

        if (!is_file($tmpFile)) {
            $this->throwException('Failed to convert, ' . $tmpFile . ' not exists.');
        }

        if (!copy($tmpFile, $file)) {
            $this->throwException('Failed to convert, ' . $file . ' not exists.');
        }

        return $file;
    }
}