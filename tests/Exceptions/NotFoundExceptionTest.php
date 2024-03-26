<?php

namespace Tests\Exceptions;

use Tests\BaseTestCase;
use Ordinary9843\Exceptions\NotFoundException;

class NotFoundExceptionTest extends BaseTestCase
{
    /**
     * @return void
     */
    public function testConstructorShouldSetValuesProperly(): void
    {
        $message = 'message';
        $code = NotFoundException::CODE_DEFAULT;
        $detail = ['detail' => 'detail'];
        $exception = new NotFoundException($message, $code, $detail);
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
        $exception = new NotFoundException('detail', NotFoundException::CODE_DEFAULT, $detail);
        $this->assertSame($detail, $exception->getDetail());
    }
}
