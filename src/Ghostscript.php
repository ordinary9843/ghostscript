<?php

namespace Ordinary9843;

use Ordinary9843\Configs\Config;
use Ordinary9843\Handlers\Handler;
use Ordinary9843\Factories\HandlerFactory;
use Ordinary9843\Exceptions\InvalidMethodException;

/**
 * @method string convert(string $file, float $version)
 * @method float guess(string $file)
 * @method string merge(string $file, array $files)
 * @method array split(string $file, string $path)
 * @method void setBinPath(string $binPath)
 * @method string getBinPath()
 * @method void setTmpPath(string $tmpPath)
 * @method string getTmpPath()
 * @method void setOptions(array $options)
 * @method array getOptions()
 * @method array getMessages(string $type = null)
 * @method bool hasMessages(string $type = null)
 */
class Ghostscript
{
    /** @var Handler */
    private $handler = null;

    /**
     * @param string $binPath
     * @param string $tmpPath
     */
    public function __construct(string $binPath = '', string $tmpPath = '')
    {
        $this->handler = HandlerFactory::create('', new Config([
            'binPath' => $binPath,
            'tmpPath' => $tmpPath
        ]));
    }

    /**
     * @param string $name
     * @param array $arguments
     * 
     * @return mixed
     * 
     * @throws InvalidMethodException
     */
    public function __call(string $name, array $arguments)
    {
        switch ($name) {
            case 'convert':
            case 'guess':
            case 'merge':
            case 'split':
                return $this->execute($name, $arguments);
            case 'setBinPath':
                return $this->handler->setBinPath(current($arguments));
            case 'getBinPath':
                return $this->handler->getBinPath();
            case 'setTmpPath':
                return $this->handler->setTmpPath(current($arguments));
            case 'getTmpPath':
                return $this->handler->getTmpPath();
            case 'setOptions':
                return $this->handler->setOptions(...$arguments);
            case 'getOptions':
                return $this->handler->getOptions();
            case 'getMessages':
                return $this->handler->getMessages();
            case 'hasMessages':
                return $this->handler->hasMessages(current($arguments));
            default:
                throw new InvalidMethodException('Invalid method: ' . $name, 0, null, [
                    'arguments' => $arguments
                ]);
        }
    }

    /**
     * @param string $name
     * @param array $arguments
     * 
     * @return mixed
     */
    private function execute(string $name, array $arguments)
    {
        $this->handler = HandlerFactory::create($name, $this->handler->getConfig());

        return $this->handler->execute(...$arguments);
    }
}
