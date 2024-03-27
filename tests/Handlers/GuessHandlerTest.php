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
}
