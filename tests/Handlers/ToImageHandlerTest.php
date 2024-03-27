<?php

namespace Tests\Handlers;

use Tests\BaseTestCase;
use Ordinary9843\Handlers\ToImageHandler;
use Ordinary9843\Constants\ToImageConstant;
use Ordinary9843\Exceptions\HandlerException;

class ToImageHandlerTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testExecuteShouldSucceed(): void
    {
        $handler = new ToImageHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertCount(3, $handler->execute(dirname(__DIR__, 2) . '/files/convert/test.pdf', '/tmp/mock/files'));
    }

    /**
     * @return void
     */
    public function testExecuteWhenTypeEqualJpegShouldSucceed(): void
    {
        $handler = new ToImageHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertCount(3, $handler->execute(dirname(__DIR__, 2) . '/files/to-image/test.pdf', '/tmp/mock/files'), ToImageConstant::TYPE_JPEG);
    }

    /**
     * @return void
     */
    public function testExecuteWhenTypeEqualPngShouldSucceed(): void
    {
        $handler = new ToImageHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertCount(3, $handler->execute(dirname(__DIR__, 2) . '/files/to-image/test.pdf', '/tmp/mock/files'), ToImageConstant::TYPE_PNG);
    }

    /**
     * @return void
     */
    public function testExecuteWhenFilenameHasChineseShouldSucceed(): void
    {
        $handler = new ToImageHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertCount(1, $handler->execute(dirname(__DIR__, 2) . '/files/gs_ -test/中文.pdf', '/tmp/mock/files'));
    }

    /**
     * @return void
     */
    public function testExecuteFailedShouldThrowHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $handler = new ToImageHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->setOptions([
            'test' => true
        ]);
        $handler->execute(dirname(__DIR__, 2) . '/files/to-image/test.pdf', '/tmp/mock/files');
    }
}
