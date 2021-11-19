<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Ghostscript;

class GhostscriptTest extends TestCase
{
    private $ghostscript;
    private $testFile = __DIR__ . '/../files/test.pdf';

    /**
     * This method is called before each test.
     * 
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $isWindows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');

        if ($isWindows === true) {
            $binPath = 'C:\gs\gs9.50\bin\gswin64c.exe';
            // $binPath = 'C:\Program Files\gs\gs9.55.0\gswin64c.exe';
            $tmpPath = 'C:\Windows\TEMP';
        } else {
            $binPath = '/usr/local/gs';
            $tmpPath = '/tmp';
        }

        $this->ghostscript = new Ghostscript($binPath, $tmpPath);
    }

    /**
     * Test guess PDF version
     * 
     * @return void
     */
    public function testGuess()
    {
        $version = $this->ghostscript->guess($this->testFile);

        $this->assertEquals($version, 1.4);
    }

    /**
     * Test convert PDF version
     * 
     * @return void
     */
    public function testConvert()
    {
        $newVersion = 1.5;
        $this->ghostscript->convert($this->testFile, $newVersion);
        $version = $this->ghostscript->guess($this->testFile);

        $this->assertEquals($version, $newVersion);

        $oldVersion = 1.4;
        $this->ghostscript->convert($this->testFile, $oldVersion);
        $version = $this->ghostscript->guess($this->testFile);

        $this->assertEquals($version, $oldVersion);
    }

    /**
     * Test delete temporary PDF
     * 
     * @return void
     */
    public function testDeleteTmpFile()
    {
        $this->ghostscript->deleteTmpFile(true);

        $tmpFileCount = $this->ghostscript->getTmpFileCount();

        $this->assertEquals($tmpFileCount, 0);
    }
}