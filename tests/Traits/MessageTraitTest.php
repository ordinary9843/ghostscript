<?php

namespace Tests\Traits;

use PHPUnit\Framework\TestCase;
use Ordinary9843\Traits\MessageTrait;
use Ordinary9843\Constants\MessageConstant;

class MessageTraitTest extends TestCase
{
    use MessageTrait;

    /**
     * @return void
     */
    public function testShouldArrayHasKeyWhenGetMessages(): void
    {
        $messages = $this->getMessages();
        $this->assertArrayHasKey(MessageConstant::TYPE_INFO, $messages);
        $this->assertArrayHasKey(MessageConstant::TYPE_ERROR, $messages);
    }

    /**
     * @return void
     */
    public function testShouldNotEmptyInfoMessageWhenGetMessages(): void
    {
        $this->addMessage(MessageConstant::TYPE_INFO, 'Message.');
        $this->assertNotEmpty($this->getMessages()[MessageConstant::TYPE_INFO]);
        $this->assertNotEmpty($this->getMessages(MessageConstant::TYPE_INFO));
    }

    /**
     * @return void
     */
    public function testShouldNotEmptyErrorMessageWhenGetMessages(): void
    {
        $this->addMessage(MessageConstant::TYPE_ERROR, 'Message.');
        $this->assertNotEmpty($this->getMessages()[MessageConstant::TYPE_ERROR]);
        $this->assertNotEmpty($this->getMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testHasMessagesShouldReturnTrueWhenTypeEqualNull(): void
    {
        $this->addMessage(MessageConstant::TYPE_INFO, 'Message.');
        $this->assertTrue($this->hasMessages());
    }

    /**
     * @return void
     */
    public function testHasMessagesShouldReturnFalseWhenTypeEqualNull(): void
    {
        $this->assertFalse($this->hasMessages());
    }

    /**
     * @return void
     */
    public function testHasMessagesShouldReturnTrueWhenTypeEqualInfo(): void
    {
        $this->addMessage(MessageConstant::TYPE_INFO, 'Message.');
        $this->assertTrue($this->hasMessages(MessageConstant::TYPE_INFO));
    }

    /**
     * @return void
     */
    public function testHasMessagesShouldReturnFalseWhenTypeEqualInfo(): void
    {
        $this->assertFalse($this->hasMessages(MessageConstant::TYPE_INFO));
    }

    /**
     * @return void
     */
    public function testHasMessagesShouldReturnTrueWhenTypeEqualError(): void
    {
        $this->addMessage(MessageConstant::TYPE_ERROR, 'Message.');
        $this->assertTrue($this->hasMessages(MessageConstant::TYPE_ERROR));
    }

    /**
     * @return void
     */
    public function testHasMessagesShouldReturnFalseWhenTypeEqualError(): void
    {
        $this->assertFalse($this->hasMessages(MessageConstant::TYPE_ERROR));
    }
}
