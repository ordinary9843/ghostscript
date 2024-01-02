<?php

namespace Ordinary9843\Interfaces;

interface HandlerInterface
{
    /**
     * @param array ...$options
     * 
     * @return mixed
     */
    public function execute(...$options);
}
