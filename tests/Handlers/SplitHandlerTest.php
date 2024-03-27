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
    public function testExecuteShouldSucceed(): void
    {
        $handler = new SplitHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $file = dirname(__DIR__, 2) . '/files/split/test.pdf';
        $this->assertCount(3, $handler->execute($file, '/tmp/mock/files'));
    }

    /**
     * @return void
     */
    public function testExecuteWhenFilenameHasChineseShouldSucceed(): void
    {
        $handler = new SplitHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $file = dirname(__DIR__, 2) . '/files/gs_ -test/中文.pdf';
        $this->assertCount(1, $handler->execute($file, '/tmp/mock/files'));
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
        $file = dirname(__DIR__, 2) . '/files/split/test.pdf';
        $handler->execute($file, '/tmp/mock/files');
    }
}
