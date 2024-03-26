<?php

namespace Tests\Exceptions;

use Tests\BaseTestCase;
use Ordinary9843\Exceptions\HandlerException;

class HandlerExceptionTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testConstructorShouldSetValuesProperly(): void
    {
        $message = 'message';
        $code = HandlerException::CODE_DEFAULT;
        $detail = ['detail' => 'detail'];
        $exception = new HandlerException($message, $code, $detail);
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
        $this->assertEquals($detail, $exception->getDetail());
    }

    /**
     * @return void
     */
    public function testGetDetailShouldReturnExpectedDetail(): void
    {
        $detail = ['detail' => 'detail'];
        $exception = new HandlerException('detail', HandlerException::CODE_DEFAULT, $detail);
        $this->assertSame($detail, $exception->getDetail());
    }
}
