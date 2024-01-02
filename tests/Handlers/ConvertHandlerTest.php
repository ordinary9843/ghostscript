<?php

namespace Tests\Handlers;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Configs\Config;
use Ordinary9843\Cores\FileSystem;
use Ordinary9843\Helpers\EnvHelper;
use Ordinary9843\Handlers\ConvertHandler;
use Ordinary9843\Constants\MessageConstant;

class ConvertHandlerTest extends TestCase
{
    /**
     * @return void
     */
    public function testExecuteWithExistFileShouldSucceed(): void
    {
        $file = dirname(__DIR__, 2) . '/files/convert/test.pdf';
        $convertHandler = new ConvertHandler();
        $convertHandler->setBinPath(EnvHelper::get('GS_BIN_PATH'));
        $convertedFile = $convertHandler->execute($file, 1.5);
        $this->assertEquals($file, $convertedFile);
        $this->assertFileExists($convertedFile);
        $this->assertFalse($convertHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testExecuteWithExistFileShouldSucceedWhenFilenameHasChinese(): void
    {
        $file = dirname(__DIR__, 2) . '/files/gs_ -test/中文.pdf';
        $convertHandler = new ConvertHandler();
        $convertHandler->setBinPath(EnvHelper::get('GS_BIN_PATH'));
        $convertedFile = $convertHandler->execute($file, 1.5);
        $this->assertEquals($file, $convertedFile);
        $this->assertFileExists($convertedFile);
        $this->assertFalse($convertHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testExecuteWithNotExistFileShouldReturnErrorMessage(): void
    {
        $fileSystem = $this->createMock(FileSystem::class);
        $fileSystem->method('isFile')->willReturn(false);
        $fileSystem->method('isValid')->willReturn(true);
        $file = dirname(__DIR__, 2) . '/files/convert/test.pdf';
        $convertHandler = new ConvertHandler(new Config([
            'binPath' => EnvHelper::get('GS_BIN_PATH'),
            'fileSystem' => $fileSystem
        ]));
        $convertedFile = $convertHandler->execute($file, 1.5);
        $this->assertNotEquals($file, $convertedFile);
        $this->assertFileNotExists($convertedFile);
        $this->assertTrue($convertHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testExecuteWithNotPdfShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/convert/test.pdf';
        $convertHandler = $this->getMockBuilder(ConvertHandler::class)
            ->setMethods(['isPdf', 'getConfig'])
            ->getMock();
        $convertHandler->method('getConfig')->willReturn(new Config(['binPath' => EnvHelper::get('GS_BIN_PATH')]));
        $convertHandler->method('isPdf')->willReturn(false);
        $convertedFile = $convertHandler->execute($file, 1.5);
        $this->assertNotEquals($file, $convertedFile);
        $this->assertFileNotExists($convertedFile);
        $this->assertTrue($convertHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testExecuteFailedShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/convert/test.pdf';
        $convertHandler = new ConvertHandler(new Config([
            'binPath' => EnvHelper::get('GS_BIN_PATH')
        ]));
        $convertHandler->setOptions([
            'test' => true
        ]);
        $convertedFile = $convertHandler->execute($file, 1.5);
        $this->assertNotEquals($file, $convertedFile);
        $this->assertFileNotExists($convertedFile);
        $this->assertTrue($convertHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }
}
