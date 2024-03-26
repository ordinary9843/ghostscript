<?php

namespace Tests\Configs;

use Tests\BaseTestCase;
use Ordinary9843\Configs\Config;
use Ordinary9843\Exceptions\ConfigException;
use Ordinary9843\Exceptions\InvalidException;

class ConfigTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testCloningSingletonShouldThrowConfigException(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionCode(ConfigException::CODE_CLONE);
        clone Config::getInstance();
    }

    /**
     * @return void
     */
    public function testWakeupSingletonShouldThrowConfigException(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionCode(ConfigException::CODE_WAKEUP);
        unserialize(serialize(Config::getInstance()));
    }

    /**
     * @return void
     */
    public function testGetInstanceTwiceToBeSame(): void
    {
        $this->assertEquals(new Config(), Config::getInstance());
        $this->assertEquals(Config::getInstance(), Config::getInstance());
    }

    /**
     * @return void
     */
    public function testGetInstanceWithArgumentsInitializesArguments(): void
    {
        $arguments = [
            'binPath' => $this->getEnv('GS_BIN_PATH'),
            'tmpPath' => sys_get_temp_dir()
        ];
        $config = Config::getInstance($arguments);
        $this->assertEquals($arguments['binPath'], $config->getBinPath());
        $this->assertEquals($arguments['tmpPath'], $config->getTmpPath());
    }

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

    // /**
    //  * @return void
    //  */
    // public function testValidateBinPathShouldReturnNull(): void
    // {
    //     // $fileSystem = $this->createMock(FileSystem::class);
    //     // $fileSystem->method('isValid')
    //     //     ->willReturn(true);
    //     $config = new Config([
    //         // 'fileSystem' => $fileSystem
    //     ]);
    //     $config->validateBinPath();
    //     // $this->assertNull($config->validateBinPath());
    // }

    // /**
    //  * @return void
    //  */
    // public function testValidateBinPathShouldThrowException(): void
    // {
    //     $fileSystem = $this->createMock(FileSystem::class);
    //     $fileSystem->method('isValid')->willReturn(false);
    //     $config = new Config([
    //         'fileSystem' => $fileSystem
    //     ]);
    //     // TODO: Expect exception code
    //     $this->expectException(InvalidException::class);
    //     $this->assertNull($config->validateBinPath());
    // }
}
