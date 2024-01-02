<?php

namespace Tests\Factories;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Configs\Config;
use Ordinary9843\Handlers\Handler;
use Ordinary9843\Factories\HandlerFactory;
use Ordinary9843\Exceptions\ClassNotFoundException;

class HandlerTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreateHandlerShouldSucceed(): void
    {
        $handler = HandlerFactory::create('', new Config([
            'binPath' => 'test_bin_path_1',
            'tmpPath' => 'test_tmp_path_1'
        ]), 'arg1', 'arg2');
        $this->assertInstanceOf(Handler::class, $handler);

        $newHandler = HandlerFactory::create('', new Config([
            'binPath' => 'test_bin_path_2',
            'tmpPath' => 'test_tmp_path_2'
        ]), 'arg1', 'arg2');
        $this->assertInstanceOf(Handler::class, $newHandler);
        $this->assertEquals($handler, $newHandler);
        $this->assertEquals($handler->getBinPath(), $newHandler->getBinPath());
        $this->assertEquals($handler->getTmpPath(), $newHandler->getTmpPath());
    }

    /**
     * @return void
     */
    public function testCreateWithInvalidHandlerTypeShouldThrowsException(): void
    {
        $this->expectException(ClassNotFoundException::class);
        HandlerFactory::create('notFound', new Config([
            'binPath' => 'test_bin_path_1',
            'tmpPath' => 'test_tmp_path_1'
        ]), 'arg1', 'arg2');
    }
}
