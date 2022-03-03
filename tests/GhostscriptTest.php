<?php

namespace Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Ordinary9843\Ghostscript;

class GhostscriptTest extends TestCase
{
    /** @var string Test PDF file path */
    protected $testFile = __DIR__ . '/../files/test.pdf';

    /** @var string Test PDF fake file path */
    protected $fakeFile = __DIR__ . '/../files/fake.pdf';

    /** @var string Ghostscript binary absolute path */
    protected $binPath = '';

    /** @var string Temporary save file absolute path */
    protected $tmpPath = '';

    /** @var float Convert PDF version to 1.4 */
    protected $oldVersion = 1.4;

    /** @var float Convert PDF version to 1.5 */
    protected $newVersion = 1.5;

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

        $output = shell_exec($this->binPath . ' --version');
        $version = floatval($output);

        $this->assertNotEquals($version, 0);
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

        $this->assertEquals($tmpPath, sys_get_temp_dir());

        $ghostscript = new Ghostscript($this->binPath, sys_get_temp_dir());
        $tmpPath = $ghostscript->getTmpPath();

        $this->assertEquals($tmpPath, sys_get_temp_dir());
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

        $version = $ghostscript->guess($this->fakeFile);
        $error = $ghostscript->getError();

        $this->assertEquals($version, 0);
        $this->assertNotEquals($error, '');
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

        $ghostscript->convert($this->fakeFile, $this->newVersion);
        $error = $ghostscript->getError();

        $this->assertNotEquals($error, '');

        $ghostscript->setOptions([
            '-dPDFSETTINGS' => '/screen',
            '-dNOPAUSE'
        ]);
        $ghostscript->convert($this->testFile, $this->newVersion);
        $error = $ghostscript->getError();

        $this->assertNotEquals($error, '');

        $ghostscript->setBinPath($this->binPath);
        $ghostscript->setOptions([
            '-dCompatibilityLevel=test'
        ]);
        $ghostscript->convert($this->testFile, $this->newVersion);
        $error = $ghostscript->getError();

        $this->assertNotEquals($error, '');

        $this->expectException('Exception');

        $ghostscript->setBinPath('');
        $ghostscript->convert($this->testFile, $this->newVersion);
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