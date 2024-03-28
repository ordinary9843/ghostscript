<?php

namespace Tests;

use Tests\BaseTestCase;
use Ordinary9843\Ghostscript;
use Ordinary9843\Exceptions\InvalidException;

class GhostscriptTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testConvertShouldSucceed(): void
    {
        $this->assertIsString((new Ghostscript($this->getEnv('GS_BIN_PATH')))->convert(dirname(__DIR__, 1) . '/files/convert/test.pdf', 1.5));
    }

    /**
     * @return void
     */
    public function testGuessShouldSucceed(): void
    {
        $this->assertIsFloat((new Ghostscript($this->getEnv('GS_BIN_PATH')))->guess(dirname(__DIR__, 1) . '/files/guess/test.pdf'));
    }

    /**
     * @return void
     */
    public function testMergesShouldSucceed(): void
    {
        $this->assertIsString((new Ghostscript($this->getEnv('GS_BIN_PATH')))->merge(dirname(__DIR__, 1) . '/files/merge', 'res.pdf', [
            dirname(__DIR__, 1) . '/files/merge/part_1.pdf',
            dirname(__DIR__, 1) . '/files/merge/part_2.pdf',
            dirname(__DIR__, 1) . '/files/merge/part_3.pdf'
        ]));
    }

    /**
     * @return void
     */
    public function testSplitShouldSucceed(): void
    {
        $this->assertIsArray((new Ghostscript($this->getEnv('GS_BIN_PATH')))->split(dirname(__DIR__, 1) . '/files/split/test.pdf', dirname(__DIR__, 1) . '/files/split/parts'));
    }

    /**
     * @return void
     */
    public function testToImageShouldSucceed(): void
    {
        $this->assertIsArray((new Ghostscript($this->getEnv('GS_BIN_PATH')))->toImage(dirname(__DIR__, 1) . '/files/to-image/test.pdf', dirname(__DIR__, 1) . '/files/to-image/images'));
    }

    /**
     * @return void
     */
    public function testGetTotalPagesShouldSucceed(): void
    {
        $this->assertIsInt((new Ghostscript($this->getEnv('GS_BIN_PATH')))->getTotalPages(dirname(__DIR__, 1) . '/files/get-total-pages/test.pdf'));
    }

    /**
     * @return void
     */
    public function testClearTmpFilesShouldSucceed(): void
    {
        $this->assertNull((new Ghostscript($this->getEnv('GS_BIN_PATH')))->clearTmpFiles(dirname(__DIR__, 1) . '/files/get-total-pages/test.pdf'));
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
    public function testInvalidMethodShouldThrowException(): void
    {
        $this->expectException(InvalidException::class);
        $this->expectExceptionCode(InvalidException::CODE_METHOD);
        (new Ghostscript())->test();
    }
}
