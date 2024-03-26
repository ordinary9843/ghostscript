<?php

namespace Tests\Handlers;

use Tests\BaseTestCase;
use Ordinary9843\Configs\Config;
use Ordinary9843\Cores\FileSystem;
use Ordinary9843\Handlers\Handler;
use Ordinary9843\Exceptions\BaseException;
use Ordinary9843\Exceptions\InvalidException;

class HandlerTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testSetConfigShouldEqualGetConfig(): void
    {
        $config = new Config([
            'binPath' => $this->getEnv('GS_BIN_PATH'),
            'tmpPath' => sys_get_temp_dir()
        ]);
        $handler = new Handler();
        $handler->setConfig($config);
        $this->assertEquals($config, $handler->getConfig());
    }

    /**
     * @return void
     */
    public function testSetFileSystemShouldEqualGetFileSystem(): void
    {
        $fileSystem = new FileSystem();
        $handler = new Handler();
        $handler->setFileSystem($fileSystem);
        $this->assertEquals($fileSystem, $handler->getFileSystem());
    }

    /**
     * @return void
     */
    public function testSetBinPathShouldEqualGetBinPath(): void
    {
        $binPath = $this->getEnv('GS_BIN_PATH');
        $handler = new Handler();
        $handler->setBinPath($binPath);
        $this->assertEquals($binPath, $handler->getBinPath());
    }

    /**
     * @return void
     */
    public function testSetTmpPathShouldEqualGetTmpPath(): void
    {
        $tmpPath = sys_get_temp_dir();
        $handler = new Handler();
        $handler->setTmpPath($tmpPath);
        $this->assertEquals($tmpPath, $handler->getTmpPath());
    }

    /**
     * @return void
     */
    public function testValidateBinPathShouldReturnNull(): void
    {
        $fileSystem = $this->createMock(FileSystem::class);
        $fileSystem->method('isValid')->willReturn(true);
        $handler = new Handler();
        $handler->setFileSystem($fileSystem);
        $this->assertNull($handler->validateBinPath());
    }

    /**
     * @return void
     */
    public function testValidateBinPathShouldThrowException(): void
    {
        $fileSystem = $this->createMock(FileSystem::class);
        $fileSystem->method('isValid')->willReturn(false);
        $handler = new Handler();
        $handler->setFileSystem($fileSystem);
        $this->expectException(InvalidException::class);
        $this->assertNull($handler->validateBinPath());
    }

    /**
     * @return void
     */
    public function testSetOptionsShouldEqualGetOptions(): void
    {
        $options = [
            '-dSAFER'
        ];
        $handler = new Handler();
        $handler->setOptions($options);
        $this->assertEquals($options, $handler->getOptions());
    }

    /**
     * @return void
     */
    public function testTmpFileShouldHaveCorrectFormat(): void
    {
        $handler = new Handler();
        $this->assertStringContainsString('/ghostscript_tmp_file_', $handler->getTmpFile());
        $this->assertStringEndsWith('.pdf', $handler->getTmpFile());
    }

    /**
     * @return void
     */
    public function testCommandShouldIncludeOptions(): void
    {
        $handler = new Handler();
        $handler->clearTmpFiles(true);
        $this->assertEquals(0, $handler->getTmpFileCount());
    }

    /**
     * @return void
     */
    public function testCommandIncludesAdditionalOptionsAfterConversion(): void
    {
        $handler = new Handler();
        $command = 'gs -sDEVICE=pdfwrite -dNOPAUSE';
        $this->assertEquals($command, $handler->optionsToCommand($command));

        $handler->setOptions([
            '-dSAFER'
        ]);
        $this->assertEquals($command . ' -dSAFER', $handler->optionsToCommand($command));
    }

    /**
     * @return void
     */
    public function testGetPdfTotalPageShouldReturnGreaterThanZero(): void
    {
        $file = dirname(__DIR__, 2) . '/files/gs_ -test/test.pdf';
        $config = new Config([
            'binPath' => $this->getEnv('GS_BIN_PATH')
        ]);
        $handler = new Handler($config);
        $this->assertGreaterThan(0, $handler->getPdfTotalPage($file));
    }

    /**
     * @return void
     */
    public function testGetPdfTotalPageShouldReturnLessThanOrEqualZero(): void
    {
        $fileSystem = $this->createMock(FileSystem::class);
        $fileSystem->method('isFile')->willReturn(false);
        $fileSystem->method('isValid')->willReturn(true);
        $file = dirname(__DIR__, 2) . '/files/gs_ -test/test.pdf';
        $handler = new Handler(new Config([
            'binPath' => $this->getEnv('GS_BIN_PATH'),
            'fileSystem' => $fileSystem
        ]));
        $this->assertLessThanOrEqual(0, $handler->getPdfTotalPage($file));

        $fileSystem = $this->createMock(FileSystem::class);
        $fileSystem->method('isFile')->willReturn(true);
        $fileSystem->method('isValid')->willReturn(true);
        $file = dirname(__DIR__, 2) . '/files/gs_- test/test.txt';
        $config = new Config([
            'binPath' => $this->getEnv('GS_BIN_PATH'),
            'fileSystem' => $fileSystem
        ]);
        $methods = ['isPdf'];
        if ($this->isPhpUnitVersionInRange(self::PHPUNIT_MIN_VERSION, self::PHPUNIT_VERSION_9)) {
            $handler = $this->getMockBuilder(Handler::class)
                ->setConstructorArgs([$config])
                ->setMethods($methods)
                ->getMock();
        } else {
            $handler = $this->getMockBuilder(Handler::class)
                ->setConstructorArgs([$config])
                ->onlyMethods($methods)
                ->getMock();
        }

        $handler->method('isPdf')->willReturn(false);
        $this->assertLessThanOrEqual(0, $handler->getPdfTotalPage($file));
    }

    /**
     * @return void
     */
    public function testIsPdfShouldReturnTrue(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'pdf');
        @file_put_contents($file, '%PDF-');
        @rename($file, $file .= '.pdf');
        $handler = new Handler();
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
        $handler = new Handler();
        $this->assertFalse($handler->isPdf($file));
        @unlink($file);
    }

    /**
     * @return void
     */
    public function testExecuteShouldThrowException(): void
    {
        $handler = new Handler();
        $this->expectException(BaseException::class);
        $this->assertNull($handler->execute());
    }
}
