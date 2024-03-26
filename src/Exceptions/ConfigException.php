<?php

namespace Ordinary9843\Exceptions;

class ConfigException extends BaseException
{
    /** @var int */
    const CODE_DEFAULT = 3000;

    /** @var int */
    const CODE_CLONE = 3001;

    /** @var int */
    const CODE_WAKEUP = 3002;

    /**
     * @param string $message
     * @param int $code
     * @param array $detail
     * @param BaseException $previous
     */
    public function __construct(string $message, int $code = self::CODE_DEFAULT, array $detail = [], BaseException $previous = null)
    {
        parent::__construct($message, $code, $detail, $previous);
    }
}
