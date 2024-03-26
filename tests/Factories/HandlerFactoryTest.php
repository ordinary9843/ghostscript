<?php

namespace Tests\Factories;

use Tests\BaseTestCase;
use Ordinary9843\Handlers\BaseHandler;
use Ordinary9843\Handlers\GuessHandler;
use Ordinary9843\Handlers\MergeHandler;
use Ordinary9843\Handlers\SplitHandler;
use Ordinary9843\Handlers\ConvertHandler;
use Ordinary9843\Handlers\ToImageHandler;
use Ordinary9843\Factories\HandlerFactory;
use Ordinary9843\Exceptions\NotFoundException;

class HandlerFactoryTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testCreateBaseHandlerShouldSucceed(): void
    {
        $handler = (new HandlerFactory)->create('base');
        $this->assertInstanceOf(BaseHandler::class, $handler);
    }

    /**
     * @return void
     */
    public function testCreateConvertHandlerShouldSucceed(): void
    {
        $handler = (new HandlerFactory)->create('convert');
        $this->assertInstanceOf(ConvertHandler::class, $handler);
    }

    /**
     * @return void
     */
    public function testCreateGuessHandlerShouldSucceed(): void
    {
        $handler = (new HandlerFactory)->create('guess');
        $this->assertInstanceOf(GuessHandler::class, $handler);
    }

    /**
     * @return void
     */
    public function testCreateMergeHandlerShouldSucceed(): void
    {
        $handler = (new HandlerFactory)->create('merge');
        $this->assertInstanceOf(MergeHandler::class, $handler);
    }

    /**
     * @return void
     */
    public function testCreateSplitHandlerShouldSucceed(): void
    {
        $handler = (new HandlerFactory)->create('split');
        $this->assertInstanceOf(SplitHandler::class, $handler);
    }

    /**
     * @return void
     */
    public function testCreateToImageHandlerShouldSucceed(): void
    {
        $handler = (new HandlerFactory)->create('toImage');
        $this->assertInstanceOf(ToImageHandler::class, $handler);
    }

    /**
     * @return void
     */
    public function testCreateBaseHandlerShouldThrowNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        (new HandlerFactory)->create('');
    }
}
