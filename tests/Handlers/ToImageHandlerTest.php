<?php

namespace Tests\Handlers;

use Tests\TestCase;
use Ordinary9843\Configs\Config;
use Ordinary9843\Handlers\ToImageHandler;
use Ordinary9843\Constants\MessageConstant;
use Ordinary9843\Constants\GhostscriptConstant;

class ToImageHandlerTest extends TestCase
{
    /**
     * @return void
     */
    public function testExecuteWithExistFileShouldSucceed(): void
    {
        $toImageHandler = new ToImageHandler();
        $toImageHandler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertCount(3, $toImageHandler->execute(dirname(__DIR__, 2) . '/files/to-image/test.pdf', '/tmp/mock/files'));
        $this->assertFalse($toImageHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testExecuteWithExistFileAndTypeEqualJpegShouldSucceed(): void
    {
        $toImageHandler = new ToImageHandler();
        $toImageHandler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertCount(3, $toImageHandler->execute(dirname(__DIR__, 2) . '/files/to-image/test.pdf', '/tmp/mock/files'), GhostscriptConstant::TO_IMAGE_TYPE_JPEG);
        $this->assertFalse($toImageHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testExecuteWithExistFileAndTypeEqualPngShouldSucceed(): void
    {
        $toImageHandler = new ToImageHandler();
        $toImageHandler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertCount(3, $toImageHandler->execute(dirname(__DIR__, 2) . '/files/to-image/test.pdf', '/tmp/mock/files'), GhostscriptConstant::TO_IMAGE_TYPE_PNG);
        $this->assertFalse($toImageHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testExecuteWithExistFileShouldSucceedWhenFilenameHasChinese(): void
    {
        $toImageHandler = new ToImageHandler();
        $toImageHandler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertCount(1, $toImageHandler->execute(dirname(__DIR__, 2) . '/files/gs_ -test/中文.pdf', '/tmp/mock/files'));
        $this->assertFalse($toImageHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testExecuteWithNotExistFileShouldReturnErrorMessage(): void
    {
        $methods = ['getPdfTotalPage', 'getConfig'];
        if ($this->isPhpUnitVersionInRange(self::PHPUNIT_MIN_VERSION, self::PHPUNIT_VERSION_9)) {
            $toImageHandler = $this->getMockBuilder(ToImageHandler::class)
                ->setMethods($methods)
                ->getMock();
        } else {
            $toImageHandler = $this->getMockBuilder(ToImageHandler::class)
                ->onlyMethods($methods)
                ->getMock();
        }

        $toImageHandler->method('getConfig')->willReturn(new Config(['binPath' => $this->getEnv('GS_BIN_PATH')]));
        $toImageHandler->method('getPdfTotalPage')->willReturn(0);
        $this->assertCount(0, $toImageHandler->execute(dirname(__DIR__, 2) . '/files/to-image/test.pdf', '/tmp/mock/files'));
        $this->assertTrue($toImageHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testExecuteFailedShouldReturnErrorMessage(): void
    {
        $toImageHandler = new ToImageHandler(new Config([
            'binPath' => $this->getEnv('GS_BIN_PATH')
        ]));
        $toImageHandler->setOptions([
            'test' => true
        ]);
        $toImageHandler->execute(dirname(__DIR__, 2) . '/files/to-image/test.pdf', '/tmp/mock/files');
        $this->assertTrue($toImageHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }
}
