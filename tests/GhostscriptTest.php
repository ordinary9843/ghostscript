<?php

namespace Tests;

use Tests\TestCase;
use Ordinary9843\Ghostscript;
use Ordinary9843\Exceptions\Exception;
use Ordinary9843\Constants\MessageConstant;

class GhostscriptTest extends TestCase
{
    /**
     * @return void
     */
    public function testConvertWithExistFileShouldSucceed(): void
    {
        $this->assertIsString((new Ghostscript($this->getEnv('GS_BIN_PATH')))->convert(dirname(__DIR__, 2) . '/files/convert/test.pdf', 1.5));
    }

    /**
     * @return void
     */
    public function testGuessWithExistFileShouldSucceed(): void
    {
        $this->assertIsFloat((new Ghostscript($this->getEnv('GS_BIN_PATH')))->guess(dirname(__DIR__, 2) . '/files/guess/test.pdf'));
    }

    /**
     * @return void
     */
    public function testMergeWithExistFilesShouldSucceed(): void
    {
        $this->assertIsString((new Ghostscript($this->getEnv('GS_BIN_PATH')))->merge(dirname(__DIR__, 2) . '/files/merge/test.pdf', [
            dirname(__DIR__, 2) . '/files/merge/part_1.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_2.pdf',
            dirname(__DIR__, 2) . '/files/merge/part_3.pdf'
        ]));
    }

    /**
     * @return void
     */
    public function testSplitWithExistFilesShouldSucceed(): void
    {
        $this->assertIsArray((new Ghostscript($this->getEnv('GS_BIN_PATH')))->split(dirname(__DIR__, 2) . '/files/split/test.pdf', dirname(__DIR__, 2) . '/files/split'));
    }

    /**
     * @return void
     */
    public function testSetBinPathShouldEqualGetBinPath(): void
    {
        $ghostscript = new Ghostscript();
        $binPath = $this->getEnv('GS_BIN_PATH');
        $ghostscript->setBinPath($binPath);
        $this->assertEquals($binPath, $ghostscript->getBinPath());
    }

    /**
     * @return void
     */
    public function testSetTmpPathShouldEqualGetTmpPath(): void
    {
        $ghostscript = new Ghostscript();
        $tmpPath = sys_get_temp_dir();
        $ghostscript->setTmpPath($tmpPath);
        $this->assertEquals($tmpPath, $ghostscript->getTmpPath());
    }

    /**
     * @return void
     */
    public function testSetOptionsShouldEqualGetOptions(): void
    {
        $ghostscript = new Ghostscript();
        $options = ['-dSAFER'];
        $ghostscript->setOptions($options);
        $this->assertEquals($options, $ghostscript->getOptions());
    }

    /**
     * @return void
     */
    public function testGetMessagesShouldReturnArrayWhenTypeEqualNull(): void
    {
        $this->assertIsArray((new Ghostscript())->getMessages());
    }

    /**
     * @return void
     */
    public function testGetMessagesShouldReturnArrayWhenTypeEqualInfo(): void
    {
        $this->assertIsArray((new Ghostscript())->getMessages(MessageConstant::TYPE_INFO));
    }

    /**
     * @return void
     */
    public function testGetMessagesShouldReturnArrayWhenTypeEqualError(): void
    {
        $this->assertIsArray((new Ghostscript())->getMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testHasMessagesShouldReturnFalseWhenTypeEqualNull(): void
    {
        $this->assertFalse((new Ghostscript())->hasMessages());
    }

    /**
     * @return void
     */
    public function testHasMessagesShouldReturnFalseWhenTypeEqualInfo(): void
    {
        $this->assertFalse((new Ghostscript())->hasMessages(MessageConstant::TYPE_INFO));
    }

    /**
     * @return void
     */
    public function testHasMessagesShouldReturnFalseWhenTypeEqualError(): void
    {
        $this->assertFalse((new Ghostscript())->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testInvalidMethodShouldThrowException(): void
    {
        $this->expectException(Exception::class);
        (new Ghostscript())->test();
    }
}
