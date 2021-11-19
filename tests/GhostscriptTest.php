<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Ghostscript;

class GhostscriptTest extends TestCase
{
    /** @var string Test pdf file path */
    private $testFile = __DIR__ . '/../files/test.pdf';

    /** @var string Ghostscript binary absolute path */
    private $binPath = '';

    /** @var string Temporary save file absolute path */
    private $tmpPath = '';

    /** @var float Convert PDF version to 1.4 */
    private $oldVersion = 1.4;

    /** @var float Convert PDF version to 1.5 */
    private $newVersion = 1.5;

    /**
     * This method is called before each test
     * 
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $isWindows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');

        if ($isWindows === true) {
            $this->binPath = 'C:\gs\gs9.55.0\bin\gswin64c.exe';
        } else {
            $this->binPath = '/usr/bin/gs';
        }

        $this->tmpPath = sys_get_temp_dir();
    }

    /**
     * Test Ghostscript binary absolute path
     * 
     * @return void
     */
    public function testBinPath()
    {
        $ghostscript = new Ghostscript($this->binPath);
        $binPath = $ghostscript->getBinPath();

        $this->assertEquals($binPath, $this->binPath);
    }

    /**
     * Test temporary save file absolute path
     * 
     * @return void
     */
    public function testTmpPath()
    {
        $ghostscript = new Ghostscript();
        $tmpPath = $ghostscript->getTmpPath();

        $this->assertEquals($tmpPath, $this->tmpPath);
    }

    /**
     * Test check Ghostscript binary absolute path does it exist
     * 
     * @return void
     */
    public function testValidateBinPath()
    {
        $this->expectExceptionMessage('The ghostscript binary path is not set.');

        $ghostscript = new Ghostscript();
        $ghostscript->validateBinPath();
    }

    /**
     * Test guess PDF version.
     * 
     * @return void
     */
    public function testGuess()
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $version = $ghostscript->guess($this->testFile);

        $this->assertContains($version, [
            $this->oldVersion,
            $this->newVersion
        ]);
    }

    /**
     * Test convert PDF version
     * 
     * @return void
     */
    public function testConvert()
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $ghostscript->convert($this->testFile, $this->newVersion);
        $version = $ghostscript->guess($this->testFile);

        $this->assertEquals($version, $this->newVersion);

        $ghostscript->convert($this->testFile, $this->oldVersion);
        $version = $ghostscript->guess($this->testFile);

        $this->assertEquals($version, $this->oldVersion);
    }

    /**
     * Test delete temporary PDF
     * 
     * @return void
     */
    public function testDeleteTmpFile()
    {
        $ghostscript = new Ghostscript($this->binPath, $this->tmpPath);
        $ghostscript->deleteTmpFile(true);
        $tmpFileCount = $ghostscript->getTmpFileCount();

        $this->assertEquals($tmpFileCount, 0);
    }
}