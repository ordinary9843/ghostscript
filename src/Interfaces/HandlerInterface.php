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

    /**
     * @param array $options
     *
     * @return void
     */
    public function setOptions(array $options): void;

    /**
     * @return array
     */
    public function getOptions(): array;

    /**
     * @return void
     */
    public function clearTmpFiles(): void;
}
