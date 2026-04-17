<?php

declare(strict_types=1);

namespace Tests\Handlers;

use Tests\BaseTestCase;
use Ordinary9843\Handlers\ToImageHandler;
use Ordinary9843\Exceptions\HandlerException;

class ToImageHandlerEdgeCaseTest extends BaseTestCase
{
    public function testExecuteWithInvalidImageTypeThrowsHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $handler = new ToImageHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute(
            dirname(__DIR__, 2) . '/files/to-image/test.pdf',
            sys_get_temp_dir(),
            'bmp'
        );
    }

    public function testExecuteWithEmptyFilePathThrowsHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $handler = new ToImageHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute('', sys_get_temp_dir(), 'jpeg');
    }

    public function testExecuteWithNonExistentFileThrowsHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $handler = new ToImageHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute('/nonexistent/file.pdf', sys_get_temp_dir(), 'jpeg');
    }
}
