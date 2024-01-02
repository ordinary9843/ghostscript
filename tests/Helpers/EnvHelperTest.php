<?php

namespace Tests\Helpers;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Helpers\EnvHelper;

class EnvHelperTest extends TestCase
{
    public function testGetReturnsEmptyStringForNonExistentKey()
    {
        $this->assertEquals('', EnvHelper::get('NOT_EXIST_PATH'));
    }

    public function testGetReturnsDefaultValueForNonExistentKey()
    {
        $default = 'default';
        $this->assertEquals($default, EnvHelper::get('NOT_EXIST_PATH', $default));
    }
}
