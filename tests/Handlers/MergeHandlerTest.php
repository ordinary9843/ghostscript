<?php

namespace Tests\Handlers;

use Tests\TestCase;
use Ordinary9843\Configs\Config;
use Ordinary9843\Handlers\MergeHandler;
use Ordinary9843\Constants\MessageConstant;

class MergeHandlerTest extends TestCase
{
    /**
     * @return void
     */
    public function testExecuteWithExistFileShouldSucceed(): void
    {
        $file = dirname(__DIR__, 2) . '/files/merge/test.pdf';
        $mergeHandler = new MergeHandler();
        $mergeHandler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $mergedFile = $mergeHandler->execute($file, [
            dirname(__DIR__, 2) . '/files/merge/part_1.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_2.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_3.pdf'
        ]);
        $this->assertEquals($file, $mergedFile);
        $this->assertFileExists($mergedFile);
        $this->assertFalse($mergeHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testExecuteWithExistFileShouldSucceedWhenFilenameHasChinese(): void
    {
        $file = dirname(__DIR__, 2) . '/files/merge/test.pdf';
        $mergeHandler = new MergeHandler();
        $mergeHandler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $mergedFile = $mergeHandler->execute($file, [
            dirname(__DIR__, 2) . '/files/gs_ -test/中文.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_1.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_2.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_3.pdf'
        ]);
        $this->assertEquals($file, $mergedFile);
        $this->assertFileExists($mergedFile);
        $this->assertFalse($mergeHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testExecuteWithNotExistFileShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/merge/test.pdf';
        $mergeHandler = new MergeHandler();
        $mergeHandler->setBinPath($this->getEnv('GS_BIN_PATH'));
        $mergedFile = $mergeHandler->execute($file, [
            dirname(__DIR__, 2) . '/files/merge/part_1.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_2.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_3.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_4.pdf'
        ]);
        $this->assertEquals($file, $mergedFile);
        $this->assertFileExists($mergedFile);
        $this->assertTrue($mergeHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testExecuteWithNotPdfShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/merge/test.pdf';
        $methods = ['isPdf', 'getConfig'];
        if ($this->isPhpUnitVersionInRange(self::PHPUNIT_MIN_VERSION, self::PHPUNIT_VERSION_9)) {
            $mergeHandler = $this->getMockBuilder(MergeHandler::class)
                ->setMethods($methods)
                ->getMock();
        } else {
            $mergeHandler = $this->getMockBuilder(MergeHandler::class)
                ->onlyMethods($methods)
                ->getMock();
        }

        $mergeHandler->method('getConfig')->willReturn(new Config(['binPath' => $this->getEnv('GS_BIN_PATH')]));
        $mergeHandler->method('isPdf')->willReturn(false);
        $mergedFile = $mergeHandler->execute($file, [
            dirname(__DIR__, 2) . '/files/merge/part_1.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_2.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_3.pdf'
        ]);
        $this->assertEquals($file, $mergedFile);
        $this->assertFileExists($mergedFile);
        $this->assertTrue($mergeHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testExecuteFailedShouldReturnErrorMessage(): void
    {
        $file = dirname(__DIR__, 2) . '/files/merge/test.pdf';
        $mergeHandler = new MergeHandler(new Config([
            'binPath' => $this->getEnv('GS_BIN_PATH')
        ]));
        $mergeHandler->setOptions([
            'test' => true
        ]);
        $mergedFile = $mergeHandler->execute($file, [
            dirname(__DIR__, 2) . '/files/merge/part_1.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_2.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_3.pdf'
        ]);
        $this->assertNotEquals($file, $mergedFile);
        $this->isPhpUnitVersionInRange(self::PHPUNIT_MIN_VERSION, self::PHPUNIT_VERSION_9) ? $this->assertFileNotExists($mergedFile) : $this->assertFileDoesNotExist($mergedFile);
        $this->assertTrue($mergeHandler->hasMessages(MessageConstant::TYPE_ERROR));
    }
}
