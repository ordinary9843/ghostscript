<?php

namespace Tests\Handlers;

use ReflectionClass;
use Tests\BaseTestCase;
use Ordinary9843\Exceptions\HandlerException;
use Ordinary9843\Handlers\GetTotalPagesHandler;

class GetTotalPagesHandlerTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testArgumentsMappingWhenProvidedInputs()
    {
        $handler = new GetTotalPagesHandler();
        $reflection = new ReflectionClass($handler);
        $property = $reflection->getProperty('argumentsMapping');
        $property->setAccessible(true);
        $argumentsMapping = $property->getValue($handler);
        $this->assertCount(1, $argumentsMapping);
        $this->assertContains('file', $argumentsMapping);
    }

    /**
     * @return void
     */
    public function testExecuteShouldSucceed(): void
    {
        $file = dirname(__DIR__, 2) . '/files/get-total-pages/test.pdf';
        $handler = new GetTotalPagesHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $totalPages = $handler->execute($file);
        $this->assertEquals(3, $totalPages);
        $this->assertFileExists($file);
    }

    /**
     * @return void
     */
    public function testExecuteWhenFilenameHasChineseShouldSucceed(): void
    {
        $file = dirname(__DIR__, 2) . '/files/gs_ -test/中文.pdf';
        $handler = new GetTotalPagesHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $totalPages = $handler->execute($file);
        $this->assertEquals(1, $totalPages);
        $this->assertFileExists($file);
    }

    /**
     * @return void
     */
    public function testExecuteWhenFileDoesNotExistShouldThrowHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $file = dirname(__DIR__, 2) . '/files/get-total-pages/part_1.pdf';
        $handler = new GetTotalPagesHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute($file);
    }

    /**
     * @return void
     */
    public function testExecuteWhenFileTypeNotMatchShouldThrowHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $file = dirname(__DIR__, 2) . '/files/get-total-pages/test.txt';
        $handler = new GetTotalPagesHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute($file);
    }
}
