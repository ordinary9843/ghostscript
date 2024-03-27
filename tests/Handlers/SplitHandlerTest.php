<?php

namespace Tests\Handlers;

use Tests\BaseTestCase;
use Ordinary9843\Handlers\SplitHandler;
use Ordinary9843\Exceptions\HandlerException;

class SplitHandlerTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testExecuteWithExistFileShouldSucceed(): void
    {
        $handler = new SplitHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertCount(3, $handler->execute(dirname(__DIR__, 2) . '/files/split/test.pdf', '/tmp/mock/files'));
    }

    /**
     * @return void
     */
    public function testExecuteWhenFilenameHasChineseShouldSucceed(): void
    {
        $handler = new SplitHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertCount(1, $handler->execute(dirname(__DIR__, 2) . '/files/gs_ -test/中文.pdf', '/tmp/mock/files'));
    }

    /**
     * @return void
     */
    public function testExecuteFailedShouldShouldThrowHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $handler = new SplitHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->setOptions([
            'test' => true
        ]);
        $handler->execute(dirname(__DIR__, 2) . '/files/split/test.pdf', '/tmp/mock/files');
    }
}
