<?php

namespace Ordinary9843\Factories;

use Ordinary9843\Configs\Config;
use Ordinary9843\Handlers\Handler;
use Ordinary9843\Interfaces\HandlerInterface;
use Ordinary9843\Exceptions\NotFoundException;

class HandlerFactory
{
    /** @var array */
    private static $handlers = [];

    /**
     * @param string $handlerType
     * 
     * @return HandlerInterface
     * 
     * @throws NotFoundException
     */
    public static function create(string $handlerType): HandlerInterface
    {
        $class = 'Ordinary9843\\Handlers\\' . ucfirst($handlerType) . 'Handler';
        if (!isset(self::$handlers[$class])) {
            if (!class_exists($class)) {
                throw new NotFoundException('Handler class "' . $class . '" does not exist.', NotFoundException::CODE_CLASS);
            }
            self::$handlers[$class] = new $class();
        }

        return self::$handlers[$class];
    }
}
