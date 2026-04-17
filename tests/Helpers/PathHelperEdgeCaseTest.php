<?php

declare(strict_types=1);

namespace Tests\Helpers;

use Tests\BaseTestCase;
use Ordinary9843\Helpers\PathHelper;

class PathHelperEdgeCaseTest extends BaseTestCase
{
    public function testEmptyStringReturnsEmptyString(): void
    {
        $this->assertEquals('', PathHelper::convertPathSeparator(''));
    }

    public function testBackslashesAreConverted(): void
    {
        $result = PathHelper::convertPathSeparator('usr\\bin\\gs');
        $this->assertEquals(implode(DIRECTORY_SEPARATOR, ['usr', 'bin', 'gs']), $result);
    }

    public function testMixedSeparatorsAreNormalized(): void
    {
        $result = PathHelper::convertPathSeparator('usr/bin\\gs');
        $this->assertEquals(implode(DIRECTORY_SEPARATOR, ['usr', 'bin', 'gs']), $result);
    }

    public function testAlreadyNormalizedPathIsUnchanged(): void
    {
        $path = implode(DIRECTORY_SEPARATOR, ['usr', 'bin', 'gs']);
        $this->assertEquals($path, PathHelper::convertPathSeparator($path));
    }

    public function testPathWithSpacesIsPreserved(): void
    {
        $result = PathHelper::convertPathSeparator('path/with spaces/file.pdf');
        $this->assertEquals(implode(DIRECTORY_SEPARATOR, ['path', 'with spaces', 'file.pdf']), $result);
    }
}
