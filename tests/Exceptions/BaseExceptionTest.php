<?php

namespace Tests\Exceptions;

use Tests\BaseTestCase;
use Ordinary9843\Exceptions\BaseException;

class BaseExceptionTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testConstructorShouldSetValuesProperly(): void
    {
        $message = 'message';
        $code = BaseException::CODE_DEFAULT;
        $detail = ['detail' => 'detail'];
        $exception = new BaseException($message, $code, $detail);
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
        $exception = new BaseException('detail', BaseException::CODE_DEFAULT, $detail);
        $this->assertSame($detail, $exception->getDetail());
    }
}
