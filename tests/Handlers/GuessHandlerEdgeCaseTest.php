<?php

declare(strict_types=1);

namespace Tests\Handlers;

use Tests\BaseTestCase;
use Ordinary9843\Handlers\GuessHandler;
use Ordinary9843\Exceptions\HandlerException;

class GuessHandlerEdgeCaseTest extends BaseTestCase
{
    public function testExecuteReturnsZeroForPdfWithoutVersionHeader(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'test');
        @rename($file, $file .= '.pdf');
        @file_put_contents($file, 'This is not a real PDF, no version header.');
        $handler = new GuessHandler();

        try {
            $version = $handler->execute($file);
            $this->assertEquals(0.0, $version);
        } finally {
            @unlink($file);
        }
    }

    public function testExecuteWithEmptyFilePathThrowsHandlerException(): void
    {
        $this->expectException(HandlerException::class);
        $this->expectExceptionCode(HandlerException::CODE_EXECUTE);
        $handler = new GuessHandler();
        $handler->execute('');
    }

    public function testExecuteWithNonPdfFileReturnsZeroOrThrows(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'test');
        @rename($file, $file .= '.pdf');
        @file_put_contents($file, '');
        $handler = new GuessHandler();

        try {
            $version = $handler->execute($file);
            $this->assertIsFloat($version);
        } finally {
            @unlink($file);
        }
    }
}
