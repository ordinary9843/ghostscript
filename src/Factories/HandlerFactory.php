<?php

namespace Ordinary9843\Factories;

use Ordinary9843\Interfaces\HandlerInterface;
use Ordinary9843\Exceptions\NotFoundException;

class HandlerFactory
{
    /**
     * @param string $type
     * 
     * @return HandlerInterface
     * 
     * @throws NotFoundException
     */
    public function create(string $type): HandlerInterface
    {
        $class = 'Ordinary9843\\Handlers\\' . ucfirst($type) . 'Handler';
        if (!class_exists($class)) {
            throw new NotFoundException('Class "' . $class . '" does not exist.', NotFoundException::CODE_CLASS);
        }

        return new $class();
    }
}
