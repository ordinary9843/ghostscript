<?php

declare(strict_types=1);

namespace Tests\Handlers;

use Tests\BaseTestCase;
use Ordinary9843\Handlers\SplitHandler;
use Ordinary9843\Exceptions\HandlerException;

class SplitHandlerEdgeCaseTest extends BaseTestCase
{
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

    public function testExecuteWithNonExistentFileThrowsHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $handler = new SplitHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute('/nonexistent/file.pdf', sys_get_temp_dir());
    }

    public function testExecuteWithEmptyFilePathThrowsHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $handler = new SplitHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute('', sys_get_temp_dir());
    }
}
