<?php

namespace Tests\Handlers;

use ReflectionClass;
use Tests\BaseTestCase;
use Ordinary9843\Handlers\ConvertHandler;
use Ordinary9843\Exceptions\HandlerException;

class ConvertHandlerTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testArgumentsMappingWhenProvidedInputs()
    {
        $handler = new ConvertHandler();
        $reflection = new ReflectionClass($handler);
        $property = $reflection->getProperty('argumentsMapping');
        $property->setAccessible(true);
        $argumentsMapping = $property->getValue($handler);
        $this->assertCount(2, $argumentsMapping);
        $this->assertContains('file', $argumentsMapping);
        $this->assertContains('version', $argumentsMapping);
    }

    /**
     * @return void
     */
    public function testExecuteShouldSucceed(): void
    {
        $file = dirname(__DIR__, 2) . '/files/convert/test.pdf';
        $handler = new ConvertHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $convertedFile = $handler->execute($file, 1.5);
        $this->assertEquals($file, $convertedFile);
        $this->assertFileExists($convertedFile);
    }

    /**
     * @return void
     */
    public function testExecuteWhenFilenameHasChineseShouldSucceed(): void
    {
        $file = dirname(__DIR__, 2) . '/files/gs_ -test/中文.pdf';
        $handler = new ConvertHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $convertedFile = $handler->execute($file, 1.5);
        $this->assertEquals($file, $convertedFile);
        $this->assertFileExists($convertedFile);
    }

    /**
     * @return void
     */
    public function testExecuteWhenFileDoesNotExistShouldThrowHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $file = dirname(__DIR__, 2) . '/files/convert/part_1.pdf';
        $handler = new ConvertHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute($file, 1.5);
    }

    /**
     * @return void
     */
    public function testExecuteWhenFileTypeNotMatchShouldThrowHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $file = dirname(__DIR__, 2) . '/files/convert/test.txt';
        $handler = new ConvertHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute($file, 1.5);
    }

    /**
     * @return void
     */
    public function testExecuteFailedShouldThrowHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $file = dirname(__DIR__, 2) . '/files/convert/test.pdf';
        $handler = new ConvertHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->setOptions([
            'test' => true
        ]);
        $handler->execute($file, 1.5);
    }
}
