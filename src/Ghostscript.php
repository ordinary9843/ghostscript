<?php

namespace Ordinary9843;

use Ordinary9843\Configs\Config;
use Ordinary9843\Factories\HandlerFactory;
use Ordinary9843\Exceptions\InvalidException;
use Ordinary9843\Interfaces\HandlerInterface;

/**
 * @method string convert(string $file, float $version)
 * @method float guess(string $file)
 * @method string merge(string $file, array $files)
 * @method array split(string $file, string $path)
 * @method array toImage(string $file, string $path, string $type = 'jpeg')
 * @method void setBinPath(string $binPath)
 * @method string getBinPath()
 * @method void setTmpPath(string $tmpPath)
 * @method string getTmpPath()
 * @method void setOptions(array $options)
 * @method array getOptions()
 */
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
     * @param string $name
     * @param array $arguments
     * 
     * @return mixed
     * 
     * @throws InvalidException
     */
    public function __call(string $name, array $arguments)
    {
        switch ($name) {
            case 'convert':
            case 'guess':
            case 'merge':
            case 'split':
            case 'toImage':
                // case 'getTotalPages':
                $handler = $this->createHandler($name);

                return $handler->execute(...$arguments);
            case 'getBinPath':
            case 'getTmpPath':
            case 'getOptions':
                $handler = $this->createBaseHandler();

                return $handler->{$name}();
            case 'setBinPath':
            case 'setTmpPath':
                $handler = $this->createBaseHandler();

                return $handler->{$name}(current($arguments));
            case 'setOptions':
                $handler = $this->createBaseHandler();

                return $handler->{$name}(...$arguments);
            default:
                throw new InvalidException('Invalid method: "' . $name . '".', InvalidException::CODE_METHOD, [
                    'name' => $name,
                    'arguments' => $arguments
                ]);
        }
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
