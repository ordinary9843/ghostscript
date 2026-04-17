<?php

declare(strict_types=1);

namespace Tests\Handlers;

use Tests\BaseTestCase;
use Ordinary9843\Handlers\MergeHandler;
use Ordinary9843\Exceptions\HandlerException;

class MergeHandlerEdgeCaseTest extends BaseTestCase
{
    public function testExecuteWithEmptyFilesArrayThrowsHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $handler = new MergeHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute(dirname(__DIR__, 2) . '/files/merge', 'res.pdf', []);
    }

    public function testExecuteWithAllInvalidFilesThrowsHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $handler = new MergeHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute(dirname(__DIR__, 2) . '/files/merge', 'res.pdf', [
            dirname(__DIR__, 2) . '/files/merge/nonexistent1.pdf',
            dirname(__DIR__, 2) . '/files/merge/nonexistent2.pdf',
        ]);
    }

    public function testExecuteWithOnlyOneValidFileMergesSuccessfully(): void
    {
        $path = dirname(__DIR__, 2) . '/files/merge';
        $filename = 'single.pdf';
        $handler = new MergeHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $result = $handler->execute($path, $filename, [
            dirname(__DIR__, 2) . '/files/merge/part_1.pdf',
            dirname(__DIR__, 2) . '/files/merge/nonexistent.pdf',
        ]);
        $this->assertFileExists($result);
    }
}
