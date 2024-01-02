<?php

namespace Tests\Exceptions;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Exceptions\Exception;

class ExceptionTest extends TestCase
{
    /**
     * @return void
     */
    public function testConstructorShouldSetValuesProperly(): void
    {
        $message = 'Test error message';
        $code = 1;
        $detail = ['detail' => 'Test detail'];
        $exception = new Exception($message, $code, null, $detail);
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
        $this->assertEquals($detail, $exception->getDetail());
    }

    /**
     * @return void
     */
    public function testGetDetailShouldReturnExpectedDetail(): void
    {
        $detail = ['detail' => 'Test detail'];
        $exception = new Exception('Test error message', 1, null, $detail);
        $this->assertSame($detail, $exception->getDetail());
    }
}
