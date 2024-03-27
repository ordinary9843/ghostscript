<?php

namespace Tests\Traits;

use Tests\BaseTestCase;
use Ordinary9843\Traits\FileSystemTrait;

class FileSystemTraitTest extends BaseTestCase
{
    use FileSystemTrait;

    /**
     * @return void
     */
    public function testPathShouldValid(): void
    {
        $path = '/tmp/mock/ghostscript';
        @mkdir($path, 0755, true);
        $this->assertEquals(true, $this->isValid($path));
        @rmdir($path);
    }

    /**
     * @return void
     */
    public function testPathShouldNotValid(): void
    {
        $this->assertEquals(false, $this->isValid('/tmp/mock/ghostscript'));
    }

    /**
     * @return void
     */
    public function testDirShouldExist(): void
    {
        $path = '/tmp/mock/ghostscript';
        @mkdir($path, 0755, true);
        $this->assertEquals(true, $this->isDir($path));
        @rmdir($path);
    }

    /**
     * @return void
     */
    public function testDirShouldNotExist(): void
    {
        $this->assertEquals(false, $this->isDir('/tmp/mock/ghostscript'));
    }

    /**
     * @return void
     */
    public function testFileShouldExist(): void
    {
        $path = '/tmp/mock/ghostscript';
        @mkdir($path, 0755, true);
        $file = '/tmp/mock/ghostscript/test.pdf';
        @file_put_contents($file, 'test');
        $this->assertEquals(true, $this->isFile($file));
        @unlink($file);
        @rmdir($path);
    }

    /**
     * @return void
     */
    public function testFileShouldNotExist(): void
    {
        $this->assertEquals(false, $this->isFile('/tmp/mock/ghostscript/test.pdf'));
    }

    /**
     * @return void
     */
    public function testDeleteFileShouldSucceed(): void
    {
        $path = '/tmp/mock/ghostscript';
        @mkdir($path, 0755, true);
        $file = '/tmp/mock/ghostscript/test.pdf';
        @file_put_contents($file, 'test');
        $this->assertEquals(true, $this->isFile($file));
        $this->delete($file);
        $this->assertEquals(false, $this->isFile($file));
        @rmdir($path);
    }

    /**
     * @return void
     */
    public function testDeleteDirectoryShouldSucceed(): void
    {
        $path = '/tmp/mock/ghostscript';
        @mkdir($path, 0755, true);
        $this->assertEquals(true, $this->isDir($path));
        $this->delete($path);
        $this->assertEquals(false, $this->isDir($path));
    }

    /**
     * @return void
     */
    public function testMakeDirectoryShouldSucceed(): void
    {
        $path = '/tmp/mock/ghostscript';
        $this->makeDir($path);
        $this->assertEquals(true, $this->isDir($path));
    }
}
