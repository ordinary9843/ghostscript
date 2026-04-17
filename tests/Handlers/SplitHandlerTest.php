<?php

namespace Tests\Handlers;

use Tests\BaseTestCase;
use Ordinary9843\Handlers\SplitHandler;
use Ordinary9843\Exceptions\HandlerException;

class SplitHandlerTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testExecuteShouldSucceed(): void
    {
        $handler = new SplitHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertCount(3, $handler->execute(dirname(__DIR__, 2) . '/files/split/test.pdf', '/tmp/mock/files'));
    }

    /**
     * @return void
     */
    public function testExecuteWhenFilenameHasChineseShouldSucceed(): void
    {
        $handler = new SplitHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertCount(1, $handler->execute(dirname(__DIR__, 2) . '/files/gs_ -test/中文.pdf', '/tmp/mock/files'));
    }

    /**
     * @return void
     */
    public function testExecuteFailedShouldShouldThrowHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $handler = new SplitHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->setOptions([
            'test' => true
        ]);
        $handler->execute(dirname(__DIR__, 2) . '/files/split/test.pdf', '/tmp/mock/files');
    }

    /**
     * @return void
     */
    public function testSplitOutputFilesStartAtPartOne(): void
    {
        $handler = new SplitHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $outputPath = sys_get_temp_dir() . '/gs_split_test_' . uniqid();
        $parts = $handler->execute(dirname(__DIR__, 2) . '/files/split/test.pdf', $outputPath);

        $this->assertCount(3, $parts);
        $this->assertStringEndsWith('/part_1.pdf', $parts[0]);
        $this->assertStringEndsWith('/part_2.pdf', $parts[1]);
        $this->assertStringEndsWith('/part_3.pdf', $parts[2]);
    }

    /**
     * @return void
     */
    public function testSplitOutputFilesAreCreated(): void
    {
        $handler = new SplitHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $outputPath = sys_get_temp_dir() . '/gs_split_test_' . uniqid();
        $parts = $handler->execute(dirname(__DIR__, 2) . '/files/split/test.pdf', $outputPath);

        foreach ($parts as $part) {
            $this->assertFileExists($part);
        }
    }

    /**
     * @return void
     */
    public function testExecuteWithNonExistentFileThrowsHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $handler = new SplitHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute('/nonexistent/file.pdf', sys_get_temp_dir());
    }

    /**
     * @return void
     */
    public function testExecuteWithEmptyFilePathThrowsHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $handler = new SplitHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute('', sys_get_temp_dir());
    }
}
