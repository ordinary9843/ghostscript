<?php

namespace Ordinary9843\Interfaces;

interface HandlerInterface extends BaseInterface
{
    /**
     * @param array ...$options
     * 
     * @return mixed
     */
    public function execute(...$options);
}
