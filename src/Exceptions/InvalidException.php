<?php

namespace Ordinary9843\Exceptions;

class InvalidException extends BaseException
{
    /** @var int */
    const CODE_DEFAULT = 2000;

    /** @var int */
    const CODE_METHOD = 2001;

    /** @var int */
    const CODE_FILEPATH = 2002;

    /** @var int */
    const CODE_FILE_TYPE = 2003;

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
