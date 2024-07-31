<?php

namespace Tests\Handlers;

use ReflectionClass;
use Tests\BaseTestCase;
use Ordinary9843\Handlers\BaseHandler;
use Ordinary9843\Exceptions\HandlerException;
use Ordinary9843\Exceptions\InvalidException;

class BaseHandlerTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testExecuteShouldThrowHandlerException(): void
    {
        $handler = new BaseHandler();
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $handler->execute();
    }

    /**
     * @return void
     */
    public function testSetOptionsShouldEqualGetOptions(): void
    {
        $options = [
            '-dSAFER'
        ];
        $handler = new BaseHandler();
        $handler->setOptions($options);
        $this->assertEquals($options, $handler->getOptions());
    }

    /**
     * @return void
     */
    public function testGetTmpFileShouldHaveCorrectFormat(): void
    {
        $handler = new BaseHandler();
        $this->assertStringContainsString('/ghostscript_tmp_file_', $handler->getTmpFile());
        $this->assertStringEndsWith('.pdf', $handler->getTmpFile());
    }

    /**
     * @return void
     */
    public function testClearTmpFilesShouldSucceed(): void
    {
        $handler = new BaseHandler();
        $reflection = new ReflectionClass($handler);
        $property = $reflection->getProperty('tmpFiles');
        $property->setAccessible(true);
        $property->setValue($handler, ['file1', 'file2']);
        $handler->clearTmpFiles(true);
        $this->assertEquals(0, $handler->getTmpFileCount());
    }

    /**
     * @return void
     */
    public function testCommandIncludesAdditionalOptionsAfterConversion(): void
    {
        $handler = new BaseHandler();
        $command = 'gs -sDEVICE=pdfwrite -dNOPAUSE';
        $this->assertEquals($command, $handler->optionsToCommand($command));
    }

    /**
     * @return void
     */
    public function testCommandShouldIncludeOptions(): void
    {
        $handler = new BaseHandler();
        $handler->setOptions([
            '-dSAFER'
        ]);
        $command = 'gs -sDEVICE=pdfwrite -dNOPAUSE';
        $this->assertEquals($command . ' -dSAFER', $handler->optionsToCommand($command));
    }

    /**
     * @return void
     */
    public function testIsPdfShouldReturnTrueForLowercasePdfExtension(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'pdf');
        @file_put_contents($file, '%PDF-');
        @rename($file, $file .= '.pdf');
        $handler = new BaseHandler();
        $this->assertTrue($handler->isPdf($file));
        @unlink($file);
    }

    /**
     * @return void
     */
    public function testIsPdfShouldReturnTrueForUppercasePdfExtension(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'pdf');
        @file_put_contents($file, '%PDF-');
        @rename($file, $file .= '.PDF');
        $handler = new BaseHandler();
        $this->assertTrue($handler->isPdf($file));
        @unlink($file);
    }

    /**
     * @return void
     */
    public function testIsPdfShouldReturnFalse(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'txt');
        @file_put_contents($file, 'txt');
        @rename($file, $file .= '.txt');
        $handler = new BaseHandler();
        $this->assertFalse($handler->isPdf($file));
        @unlink($file);
    }

    /**
     * @return void
     */
    public function testValidateBinPathShouldReturnNull(): void
    {
        $handler = new BaseHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertNull($handler->validateBinPath());
    }

    /**
     * @return void
     */
    public function testValidateBinPathShouldThrowException(): void
    {
        $this->expectException(InvalidException::class);
        $this->expectExceptionCode(InvalidException::CODE_FILEPATH);
        $handler = new BaseHandler();
        $handler->setBinPath('');
        $handler->validateBinPath();
    }

    /**
     * @return void
     */
    public function testConvertToTmpFile()
    {
        $handler = new BaseHandler();
        $reflection = new ReflectionClass($handler);
        $convertToTmpFileMethod = $reflection->getMethod('convertToTmpFile');
        $convertToTmpFileMethod->setAccessible(true);
        $file = tempnam(sys_get_temp_dir(), 'test');
        @file_put_contents($file, '');
        $tmpFile = $convertToTmpFileMethod->invokeArgs($handler, [$file]);
        $this->assertFileExists($tmpFile);
        $this->assertEquals('', @file_get_contents($tmpFile));

        $getTmpFilesMethod = $reflection->getMethod('getTmpFiles');
        $getTmpFilesMethod->setAccessible(true);
        $tmpFiles = $getTmpFilesMethod->invoke($handler);
        $this->assertContains($tmpFile, $tmpFiles);
    }

    /**
     * @return void
     */
    public function testAddAndRetrieveTmpFiles()
    {
        $handler = new BaseHandler();
        $reflection = new ReflectionClass($handler);
        $addTmpFileMethod = $reflection->getMethod('addTmpFile');
        $addTmpFileMethod->setAccessible(true);
        $addTmpFileMethod->invokeArgs($handler, ['file1']);
        $addTmpFileMethod->invokeArgs($handler, ['file2']);
        $getTmpFilesMethod = $reflection->getMethod('getTmpFiles');
        $getTmpFilesMethod->setAccessible(true);
        $tmpFiles = $getTmpFilesMethod->invoke($handler);
        $this->assertContains('file1', $tmpFiles);
        $this->assertContains('file2', $tmpFiles);
    }

    /**
     * @return void
     */
    public function testArgumentsMappingWhenProvidedInputs()
    {
        $handler = new BaseHandler();
        $reflection = new ReflectionClass($handler);
        $property = $reflection->getProperty('argumentsMapping');
        $property->setAccessible(true);
        $property->setValue($handler, ['arg1', 'arg2']);
        $arguments = ['value1', 'value2'];
        $method = $reflection->getMethod('mapArguments');
        $method->setAccessible(true);
        $method->invokeArgs($handler, [&$arguments]);
        $this->assertEquals([
            'arg1' => 'value1',
            'arg2' => 'value2'
        ], $arguments);
    }
}
