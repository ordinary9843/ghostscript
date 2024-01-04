<?php

namespace Tests\Handlers;

use Tests\TestCase;
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
        $guessHandler->setBinPath($this->getEnv('GS_BIN_PATH'));
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
        $guessHandler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $this->assertEquals(0.0, $guessHandler->execute($file));
        $this->isPhpUnitVersionInRange(self::PHPUNIT_MIN_VERSION, self::PHPUNIT_VERSION_9) ? $this->assertFileNotExists($file) : $this->assertFileDoesNotExist($file);
        $this->assertTrue($guessHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }
}
