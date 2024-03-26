<?php

namespace Ordinary9843\Exceptions;

class NotFoundException extends BaseException
{
    /** @var int */
    const CODE_DEFAULT = 1000;

    /** @var int */
    const CODE_CLASS = 1001;

    /** @var int */
    const CODE_FILE = 1002;

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
