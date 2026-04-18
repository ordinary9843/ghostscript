<?php

namespace Tests\Handlers;

use ReflectionClass;
use Tests\BaseTestCase;
use Ordinary9843\Handlers\CompressHandler;
use Ordinary9843\Constants\CompressConstant;
use Ordinary9843\Exceptions\HandlerException;

class CompressHandlerTest extends BaseTestCase
{
    /** @var string */
    private $sourceFile;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->sourceFile = dirname(__DIR__, 2) . '/files/compress/test.pdf';
    }

    /**
     * @return string
     */
    private function makeTmpCopy(): string
    {
        $tmp = sys_get_temp_dir() . '/compress_test_' . uniqid() . '.pdf';
        copy($this->sourceFile, $tmp);

        return $tmp;
    }

    /**
     * @return void
     */
    public function testArgumentsMappingWhenProvidedInputs(): void
    {
        $handler = new CompressHandler();
        $reflection = new ReflectionClass($handler);
        $property = $reflection->getProperty('argumentsMapping');
        $property->setAccessible(true);
        $argumentsMapping = $property->getValue($handler);
        $this->assertCount(2, $argumentsMapping);
        $this->assertContains('file', $argumentsMapping);
        $this->assertContains('quality', $argumentsMapping);
    }

    /**
     * @return void
     */
    public function testExecuteWithDefaultQualityShouldSucceed(): void
    {
        $file = $this->makeTmpCopy();
        $originalSize = filesize($file);
        $handler = new CompressHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $result = $handler->execute($file);
        $this->assertEquals($file, $result);
        $this->assertFileExists($file);
        $this->assertLessThanOrEqual($originalSize, filesize($file));
        @unlink($file);
    }

    /**
     * @return void
     */
    public function testExecuteWithScreenQualityShouldSucceed(): void
    {
        $file = $this->makeTmpCopy();
        $originalSize = filesize($file);
        $handler = new CompressHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $result = $handler->execute($file, CompressConstant::SCREEN);
        $this->assertEquals($file, $result);
        $this->assertFileExists($file);
        $this->assertLessThanOrEqual($originalSize, filesize($file));
        @unlink($file);
    }

    /**
     * @return void
     */
    public function testExecuteWithEbookQualityShouldSucceed(): void
    {
        $file = $this->makeTmpCopy();
        $originalSize = filesize($file);
        $handler = new CompressHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $result = $handler->execute($file, CompressConstant::EBOOK);
        $this->assertEquals($file, $result);
        $this->assertFileExists($file);
        $this->assertLessThanOrEqual($originalSize, filesize($file));
        @unlink($file);
    }

    /**
     * @return void
     */
    public function testExecuteWithPrinterQualityShouldSucceed(): void
    {
        $file = $this->makeTmpCopy();
        $handler = new CompressHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $result = $handler->execute($file, CompressConstant::PRINTER);
        $this->assertEquals($file, $result);
        $this->assertFileExists($file);
        $this->assertGreaterThan(0, filesize($file));
        @unlink($file);
    }

    /**
     * @return void
     */
    public function testExecuteWithPrepressQualityShouldSucceed(): void
    {
        $file = $this->makeTmpCopy();
        $originalSize = filesize($file);
        $handler = new CompressHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $result = $handler->execute($file, CompressConstant::PREPRESS);
        $this->assertEquals($file, $result);
        $this->assertFileExists($file);
        $this->assertLessThanOrEqual($originalSize, filesize($file));
        @unlink($file);
    }

    /**
     * @return void
     */
    public function testExecuteWithInvalidQualityShouldThrowHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $file = $this->makeTmpCopy();
        $handler = new CompressHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute($file, 'invalid');
        @unlink($file);
    }

    /**
     * @return void
     */
    public function testExecuteWithNonExistentFileShouldThrowHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $handler = new CompressHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute('/nonexistent/file.pdf');
    }

    /**
     * @return void
     */
    public function testExecuteWithEmptyFilePathShouldThrowHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $handler = new CompressHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute('');
    }

    /**
     * @return void
     */
    public function testExecuteWithNonPdfFileShouldThrowHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $file = dirname(__DIR__, 2) . '/files/convert/test.txt';
        $handler = new CompressHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute($file);
    }
}
