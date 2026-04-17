<?php

declare(strict_types=1);

namespace Tests\Handlers;

use Tests\BaseTestCase;
use Ordinary9843\Handlers\GetTotalPagesHandler;
use Ordinary9843\Exceptions\HandlerException;

class GetTotalPagesHandlerEdgeCaseTest extends BaseTestCase
{
    public function testExecuteWithEmptyFilePathThrowsHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $handler = new GetTotalPagesHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute('');
    }

    public function testExecuteWithNonExistentFileThrowsHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $handler = new GetTotalPagesHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute('/nonexistent/file.pdf');
    }
}
