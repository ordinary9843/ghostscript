<?php

namespace Tests\Handlers;

use Tests\TestCase;
use Ordinary9843\Configs\Config;
use Ordinary9843\Handlers\SplitHandler;
use Ordinary9843\Constants\MessageConstant;

class SplitHandlerTest extends TestCase
{
    /**
     * @return void
     */
    public function testExecuteWithExistFileShouldSucceed(): void
    {
        $splitHandler = new SplitHandler();
        $splitHandler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertCount(3, $splitHandler->execute(dirname(__DIR__, 2) . '/files/split/test.pdf', '/tmp/mock/files'));
        $this->assertFalse($splitHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testExecuteWithExistFileShouldSucceedWhenFilenameHasChinese(): void
    {
        $splitHandler = new SplitHandler();
        $splitHandler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertCount(1, $splitHandler->execute(dirname(__DIR__, 2) . '/files/gs_ -test/中文.pdf', '/tmp/mock/files'));
        $this->assertFalse($splitHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testExecuteWithNotExistFileShouldReturnErrorMessage(): void
    {
        if ($this->isPhpUnitVersionInRange(self::PHPUNIT_MIN_VERSION, self::PHPUNIT_VERSION_9)) {
            $splitHandler = $this->getMockBuilder(SplitHandler::class)
                ->setMethods(['getPdfTotalPage', 'getConfig'])
                ->getMock();
        } else {
            $splitHandler = $this->getMockBuilder(SplitHandler::class)
                ->onlyMethods(['getPdfTotalPage', 'getConfig'])
                ->getMock();
        }

        $splitHandler->method('getConfig')->willReturn(new Config(['binPath' => $this->getEnv('GS_BIN_PATH')]));
        $splitHandler->method('getPdfTotalPage')->willReturn(0);
        $this->assertCount(0, $splitHandler->execute(dirname(__DIR__, 2) . '/files/split/test.pdf', '/tmp/mock/files'));
        $this->assertTrue($splitHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testExecuteFailedShouldReturnErrorMessage(): void
    {
        $splitHandler = new SplitHandler(new Config([
            'binPath' => $this->getEnv('GS_BIN_PATH')
        ]));
        $splitHandler->setOptions([
            'test' => true
        ]);
        $splitHandler->execute(dirname(__DIR__, 2) . '/files/split/test.pdf', '/tmp/mock/files');
        $this->assertTrue($splitHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }
}
