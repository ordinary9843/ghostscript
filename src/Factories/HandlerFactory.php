<?php

namespace Ordinary9843\Factories;

use Ordinary9843\Configs\Config;
use Ordinary9843\Handlers\Handler;
use Ordinary9843\Exceptions\NotFoundException;

class HandlerFactory
{
    /** @var array */
    private static $handlers = [];

    /**
     * @param string $handlerType
     * @param Config $config
     * 
     * @return Handler
     * 
     * @throws NotFoundException
     */
    public static function create(string $handlerType, Config $config): Handler
    {
        $class = 'Ordinary9843\\Handlers\\' . ucfirst($handlerType) . 'Handler';
        if (!isset(self::$handlers[$class])) {
            if (!class_exists($class)) {
                throw new NotFoundException('Handler class "' . $class . '" does not exist.', NotFoundException::CODE_CLASS);
            }
            self::$handlers[$class] = new $class($config);
        } else {
            self::$handlers[$class]->setConfig($config);
        }

        return self::$handlers[$class];
    }
}
