<?php

namespace Tests\Handlers;

use ReflectionClass;
use Tests\BaseTestCase;
use Ordinary9843\Handlers\GuessHandler;
use Ordinary9843\Exceptions\HandlerException;

class GuessHandlerTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testArgumentsMappingWhenProvidedInputs()
    {
        $handler = new GuessHandler();
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
        $file = dirname(__DIR__, 2) . '/files/guess/test.pdf';
        $handler = new GuessHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertEquals(1.5, $handler->execute($file));
        $this->assertFileExists($file);
    }

    /**
     * @return void
     */
    public function testExecuteShouldThrowInvalidException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $file = dirname(__DIR__, 2) . '/files/guess/part_4.pdf';
        $handler = new GuessHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute($file);
    }

    /**
     * @return void
     */
    public function testExecuteReturnsZeroForPdfWithoutVersionHeader(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'test');
        @rename($file, $file .= '.pdf');
        @file_put_contents($file, 'This is not a real PDF, no version header.');
        $handler = new GuessHandler();

        try {
            $version = $handler->execute($file);
            $this->assertEquals(0.0, $version);
        } finally {
            @unlink($file);
        }
    }

    /**
     * @return void
     */
    public function testExecuteWithEmptyFilePathThrowsHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $handler = new GuessHandler();
        $handler->execute('');
    }
}
