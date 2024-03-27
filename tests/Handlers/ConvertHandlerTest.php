<?php

namespace Tests\Handlers;

use Tests\BaseTestCase;
use Ordinary9843\Handlers\ConvertHandler;
use Ordinary9843\Exceptions\HandlerException;

class ConvertHandlerTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testExecuteShouldSucceed(): void
    {
        $file = dirname(__DIR__, 2) . '/files/convert/test.pdf';
        $convertHandler = new ConvertHandler();
        $convertHandler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $convertedFile = $convertHandler->execute($file, 1.5);
        $this->assertEquals($file, $convertedFile);
        $this->assertFileExists($convertedFile);
    }

    /**
     * @return void
     */
    public function testExecuteShouldSucceedWhenFilenameHasChinese(): void
    {
        $file = dirname(__DIR__, 2) . '/files/gs_ -test/中文.pdf';
        $convertHandler = new ConvertHandler();
        $convertHandler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $convertedFile = $convertHandler->execute($file, 1.5);
        $this->assertEquals($file, $convertedFile);
        $this->assertFileExists($convertedFile);
    }

    /**
     * @return void
     */
    public function testExecuteWhenFileDoesNotExistShouldThrowHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $file = dirname(__DIR__, 2) . '/files/convert/part_1.pdf';
        $convertHandler = new ConvertHandler();
        $convertHandler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $convertHandler->execute($file, 1.5);
    }

    /**
     * @return void
     */
    public function testExecuteWhenFileTypeNotMatchShouldThrowHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $file = dirname(__DIR__, 2) . '/files/convert/test.txt';
        $convertHandler = new ConvertHandler();
        $convertHandler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $convertHandler->execute($file, 1.5);
    }

    /**
     * @return void
     */
    public function testExecuteFailedShouldThrowHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $file = dirname(__DIR__, 2) . '/files/convert/test.pdf';
        $convertHandler = new ConvertHandler();
        $convertHandler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $convertHandler->setOptions([
            'test' => true
        ]);
        $convertHandler->execute($file, 1.5);
    }
}
