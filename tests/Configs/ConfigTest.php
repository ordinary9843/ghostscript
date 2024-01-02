<?php

namespace Tests\Configs;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Configs\Config;
use ordinary9843\Cores\FileSystem;
use Ordinary9843\Helpers\EnvHelper;
use Ordinary9843\Exceptions\InvalidFilePathException;

class ConfigTest extends TestCase
{
    /**
     * @return void
     */
    public function testSetBinPathShouldEqualGetBinPath(): void
    {
        $binPath = EnvHelper::get('GS_BIN_PATH');
        $config = new Config();
        $config->setBinPath($binPath);
        $this->assertEquals($binPath, $config->getBinPath());
    }

    /**
     * @return void
     */
    public function testSetTmpPathShouldEqualGetTmpPath(): void
    {
        $tmpPath = sys_get_temp_dir();
        $config = new Config();
        $config->setTmpPath($tmpPath);
        $this->assertEquals($tmpPath, $config->getTmpPath());
    }

    /**
     * @return void
     */
    public function testValidateBinPathShouldReturnNull(): void
    {
        $fileSystem = $this->createMock(FileSystem::class);
        $fileSystem->method('isValid')->willReturn(true);
        $config = new Config([
            'fileSystem' => $fileSystem
        ]);
        $this->assertNull($config->validateBinPath());
    }

    /**
     * @return void
     */
    public function testValidateBinPathShouldThrowException(): void
    {
        $fileSystem = $this->createMock(FileSystem::class);
        $fileSystem->method('isValid')->willReturn(false);
        $config = new Config([
            'fileSystem' => $fileSystem
        ]);
        $this->expectException(InvalidFilePathException::class);
        $this->assertNull($config->validateBinPath());
    }
}
