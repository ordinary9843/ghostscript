<?php

declare(strict_types=1);

namespace Tests\Handlers;

use Tests\BaseTestCase;
use Ordinary9843\Handlers\ConvertHandler;
use Ordinary9843\Exceptions\HandlerException;

class ConvertHandlerEdgeCaseTest extends BaseTestCase
{
    public function testExecuteReturnsSameFilePathOnSuccess(): void
    {
        $file = dirname(__DIR__, 2) . '/files/convert/test.pdf';
        $handler = new ConvertHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $result = $handler->execute($file, 1.4);
        $this->assertEquals($file, $result);
        $this->assertFileExists($result);
    }

    public function testExecuteWithEmptyFilePathThrowsHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $handler = new ConvertHandler();
        $handler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $handler->execute('', 1.4);
    }
}
