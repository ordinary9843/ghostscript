<?php

declare(strict_types=1);

namespace Tests\Factories;

use Tests\BaseTestCase;
use Ordinary9843\Factories\HandlerFactory;
use Ordinary9843\Handlers\GetTotalPagesHandler;
use Ordinary9843\Exceptions\NotFoundException;

class HandlerFactoryEdgeCaseTest extends BaseTestCase
{
    public function testCreateGetTotalPagesHandlerShouldSucceed(): void
    {
        $handler = (new HandlerFactory())->create('getTotalPages');
        $this->assertInstanceOf(GetTotalPagesHandler::class, $handler);
    }

    public function testCreateWithUppercaseTypeShouldThrowNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionCode(NotFoundException::CODE_CLASS);
        (new HandlerFactory())->create('Convert');
    }

    public function testCreateWithUnknownTypeShouldThrowNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionCode(NotFoundException::CODE_CLASS);
        (new HandlerFactory())->create('nonExistentHandler');
    }
}
