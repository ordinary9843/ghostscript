<?php

namespace Tests\Helpers;

use Tests\BaseTestCase;
use Ordinary9843\Helpers\PathHelper;

class PathHelperTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testPathShouldEqualOriginPathAfterConversion(): void
    {
        $this->assertEquals(implode(DIRECTORY_SEPARATOR, ['usr', 'bin', 'gs']), PathHelper::convertPathSeparator('usr/bin/gs'));
    }

    /**
     * @return void
     */
    public function testEmptyStringReturnsEmptyString(): void
    {
        $this->assertEquals('', PathHelper::convertPathSeparator(''));
    }

    /**
     * @return void
     */
    public function testBackslashesAreConverted(): void
    {
        $result = PathHelper::convertPathSeparator('usr\\bin\\gs');
        $this->assertEquals(implode(DIRECTORY_SEPARATOR, ['usr', 'bin', 'gs']), $result);
    }

    /**
     * @return void
     */
    public function testMixedSeparatorsAreNormalized(): void
    {
        $result = PathHelper::convertPathSeparator('usr/bin\\gs');
        $this->assertEquals(implode(DIRECTORY_SEPARATOR, ['usr', 'bin', 'gs']), $result);
    }

    /**
     * @return void
     */
    public function testAlreadyNormalizedPathIsUnchanged(): void
    {
        $path = implode(DIRECTORY_SEPARATOR, ['usr', 'bin', 'gs']);
        $this->assertEquals($path, PathHelper::convertPathSeparator($path));
    }

    /**
     * @return void
     */
    public function testPathWithSpacesIsPreserved(): void
    {
        $result = PathHelper::convertPathSeparator('path/with spaces/file.pdf');
        $this->assertEquals(implode(DIRECTORY_SEPARATOR, ['path', 'with spaces', 'file.pdf']), $result);
    }
}
