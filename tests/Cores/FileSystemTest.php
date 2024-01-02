<?php

namespace Tests\Cores;

use PHPUnit\Framework\TestCase;
use ordinary9843\Cores\FileSystem;

class FileSystemTest extends TestCase
{
    /**
     * @return void
     */
    public function testPathShouldValid(): void
    {
        $fileSystem = new FileSystem();
        $path = '/tmp/mock/ghostscript';
        @mkdir($path, 0755, true);
        $this->assertEquals(true, $fileSystem->isValid($path));
        @rmdir($path);
    }

    /**
     * @return void
     */
    public function testPathShouldNotValid(): void
    {
        $fileSystem = new FileSystem();
        $this->assertEquals(false, $fileSystem->isValid('/tmp/mock/ghostscript'));
    }

    /**
     * @return void
     */
    public function testDirShouldExist(): void
    {
        $fileSystem = new FileSystem();
        $path = '/tmp/mock/ghostscript';
        @mkdir($path, 0755, true);
        $this->assertEquals(true, $fileSystem->isDir($path));
        @rmdir($path);
    }

    /**
     * @return void
     */
    public function testDirShouldNotExist(): void
    {
        $fileSystem = new FileSystem();
        $this->assertEquals(false, $fileSystem->isDir('/tmp/mock/ghostscript'));
    }

    /**
     * @return void
     */
    public function testFileShouldExist(): void
    {
        $fileSystem = new FileSystem();
        $path = '/tmp/mock/ghostscript';
        @mkdir($path, 0755, true);
        $file = '/tmp/mock/ghostscript/test.pdf';
        @file_put_contents($file, 'test');
        $this->assertEquals(true, $fileSystem->isFile($file));
        @unlink($file);
        @rmdir($path);
    }

    /**
     * @return void
     */
    public function testFileShouldNotExist(): void
    {
        $fileSystem = new FileSystem();
        $this->assertEquals(false, $fileSystem->isFile('/tmp/mock/ghostscript/test.pdf'));
    }

    /**
     * @return void
     */
    public function testShouldDeleteFile(): void
    {
        $fileSystem = new FileSystem();
        $path = '/tmp/mock/ghostscript';
        @mkdir($path, 0755, true);
        $file = '/tmp/mock/ghostscript/test.pdf';
        @file_put_contents($file, 'test');
        $this->assertEquals(true, $fileSystem->isFile($file));
        $fileSystem->delete($file);
        $this->assertEquals(false, $fileSystem->isFile($file));
        @rmdir($path);
    }

    /**
     * @return void
     */
    public function testShouldDeleteDirectory(): void
    {
        $fileSystem = new FileSystem();
        $path = '/tmp/mock/ghostscript';
        @mkdir($path, 0755, true);
        $this->assertEquals(true, $fileSystem->isDir($path));
        $fileSystem->delete($path);
        $this->assertEquals(false, $fileSystem->isDir($path));
    }
}
