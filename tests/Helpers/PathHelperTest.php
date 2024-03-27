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
}
