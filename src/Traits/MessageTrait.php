<?php

namespace Ordinary9843\Traits;

use Ordinary9843\Constants\MessageConstant;

trait MessageTrait
{
    /** @var array */
    private $messages = [
        MessageConstant::TYPE_INFO => [],
        MessageConstant::TYPE_ERROR => []
    ];

    /**
     * @param string $type
     * @return array
     */
    public function getMessages(string $type = null): array
    {
        if (in_array($type, [MessageConstant::TYPE_INFO, MessageConstant::TYPE_ERROR])) {
            return $this->messages[$type];
        }

        return $this->messages;
    }

    /**
     * @param string $type
     * @param string $message
     * 
     * @return void
     */
    public function addMessage(string $type, string $message): void
    {
        (!array_key_exists($type, $this->messages)) && $type = MessageConstant::TYPE_INFO;
        (!in_array($message, $this->messages[$type])) && $this->messages[$type][] = date('Y-m-d H:i:s') . ' [' . $type . '] ' . $message;
    }

    /**
     * @return bool
     */
    public function hasMessages(string $type = null): bool
    {
        if ($type === null) {
            return !empty(array_filter($this->messages));
        }

        return !empty($this->messages[$type]);
    }
}
