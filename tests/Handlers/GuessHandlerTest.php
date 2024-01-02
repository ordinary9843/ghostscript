<?php

namespace Tests\Handlers;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Helpers\EnvHelper;
use Ordinary9843\Handlers\GuessHandler;
use Ordinary9843\Constants\MessageConstant;

class GuessHandlerTest extends TestCase
{
    /**
     * @return void
     */
    public function testExecuteWithExistFileShouldSucceed(): void
    {
        $file = dirname(__DIR__, 2) . '/files/guess/test.pdf';
        $guessHandler = new GuessHandler();
        $guessHandler->setBinPath(EnvHelper::get('GS_BIN_PATH'));
        $this->assertEquals(1.5, $guessHandler->execute($file));
        $this->assertFileExists($file);
        $this->assertFalse($guessHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testExecuteWithNotExistFileShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/guess/part_4.pdf';
        $guessHandler = new GuessHandler();
        $guessHandler->setBinPath(EnvHelper::get('GS_BIN_PATH'));
        $this->assertEquals(0.0, $guessHandler->execute($file));
        $this->assertFileNotExists($file);
        $this->assertTrue($guessHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }
}
