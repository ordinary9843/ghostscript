<?php

declare(strict_types=1);

namespace Ordinary9843;

use Ordinary9843\Configs\Config;
use Ordinary9843\Factories\HandlerFactory;
use Ordinary9843\Exceptions\HandlerException;
use Ordinary9843\Exceptions\InvalidException;
use Ordinary9843\Interfaces\HandlerInterface;
use Ordinary9843\Constants\CompressConstant;
use Ordinary9843\Constants\ImageTypeConstant;

class Ghostscript
{
    /** @var HandlerInterface[] */
    protected $handlers = [];

    /** @var array */
    protected $arguments = [];

    /**
     * @param string $binPath
     * @param string $tmpPath
     */
    public function __construct(string $binPath = '', string $tmpPath = '')
    {
        $this->arguments = [
            'binPath' => $binPath,
            'tmpPath' => $tmpPath
        ];

        Config::initialize($this->arguments);
    }

    /**
     * @param string $file
     * @param string $quality
     *
     * @return string
     *
     * @throws HandlerException
     * @throws InvalidException
     */
    public function compress(string $file, string $quality = CompressConstant::EBOOK): string
    {
        return $this->createHandler('compress')->execute($file, $quality);
    }

    /**
     * @param string $file
     * @param float $version
     *
     * @return string
     *
     * @throws HandlerException
     * @throws InvalidException
     */
    public function convert(string $file, float $version): string
    {
        return $this->createHandler('convert')->execute($file, $version);
    }

    /**
     * @param string $file
     *
     * @return float
     *
     * @throws HandlerException
     * @throws InvalidException
     */
    public function guess(string $file): float
    {
        return $this->createHandler('guess')->execute($file);
    }

    /**
     * @param string $path
     * @param string $filename
     * @param array $files
     * @param bool $isAutoConvert
     *
     * @return string
     *
     * @throws HandlerException
     * @throws InvalidException
     */
    public function merge(string $path, string $filename, array $files, bool $isAutoConvert = true): string
    {
        return $this->createHandler('merge')->execute($path, $filename, $files, $isAutoConvert);
    }

    /**
     * @param string $file
     * @param string $path
     *
     * @return array
     *
     * @throws HandlerException
     * @throws InvalidException
     */
    public function split(string $file, string $path): array
    {
        return $this->createHandler('split')->execute($file, $path);
    }

    /**
     * @param string $file
     * @param string $path
     * @param string $type
     *
     * @return array
     *
     * @throws HandlerException
     * @throws InvalidException
     */
    public function toImage(string $file, string $path, string $type = ImageTypeConstant::JPEG): array
    {
        return $this->createHandler('toImage')->execute($file, $path, $type);
    }

    /**
     * @param string $file
     *
     * @return int
     *
     * @throws HandlerException
     * @throws InvalidException
     */
    public function getTotalPages(string $file): int
    {
        return $this->createHandler('getTotalPages')->execute($file);
    }

    /**
     * @param bool $isForceClear
     * @param int $days
     *
     * @return void
     */
    public function clearTmpFiles(bool $isForceClear = false, int $days = 7): void
    {
        $this->createBaseHandler()->clearTmpFiles($isForceClear, $days);
    }

    /**
     * @param string $binPath
     *
     * @return void
     */
    public function setBinPath(string $binPath): void
    {
        $this->createBaseHandler()->setBinPath($binPath);
    }

    /**
     * @return string
     */
    public function getBinPath(): string
    {
        return $this->createBaseHandler()->getBinPath();
    }

    /**
     * @param string $tmpPath
     *
     * @return void
     */
    public function setTmpPath(string $tmpPath): void
    {
        $this->createBaseHandler()->setTmpPath($tmpPath);
    }

    /**
     * @return string
     */
    public function getTmpPath(): string
    {
        return $this->createBaseHandler()->getTmpPath();
    }

    /**
     * @param array $options
     *
     * @return void
     */
    public function setOptions(array $options): void
    {
        $this->createBaseHandler()->setOptions($options);
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->createBaseHandler()->getOptions();
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     *
     * @throws InvalidException
     */
    public function __call(string $name, array $arguments)
    {
        throw new InvalidException('Invalid method: "' . $name . '".', InvalidException::CODE_METHOD, [
            'name' => $name,
            'arguments' => $arguments
        ]);
    }

    /**
     * @param string $name
     *
     * @return HandlerInterface
     */
    private function createHandler(string $name): HandlerInterface
    {
        if (isset($this->handlers[$name])) {
            return $this->handlers[$name];
        }

        $this->handlers[$name] = (new HandlerFactory())->create($name);

        return $this->handlers[$name];
    }

    /**
     * @return HandlerInterface
     */
    private function createBaseHandler(): HandlerInterface
    {
        return $this->createHandler('base');
    }
}
