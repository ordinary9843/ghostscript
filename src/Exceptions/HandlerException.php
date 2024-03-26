<?php

namespace Ordinary9843\Exceptions;

class HandlerException extends BaseException
{
    /** @var int */
    const CODE_DEFAULT = 4000;

    /** @var int */
    const CODE_EXECUTE = 4001;

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
