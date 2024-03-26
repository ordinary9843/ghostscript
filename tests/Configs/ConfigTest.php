<?php

namespace Tests\Configs;

use Tests\BaseTestCase;
use Ordinary9843\Configs\Config;
use ordinary9843\Cores\FileSystem;
use Ordinary9843\Exceptions\InvalidException;

class ConfigTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testSetBinPathShouldEqualGetBinPath(): void
    {
        $binPath = $this->getEnv('GS_BIN_PATH');
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
        // TODO: Expect exception code
        $this->expectException(InvalidException::class);
        $this->assertNull($config->validateBinPath());
    }
}
