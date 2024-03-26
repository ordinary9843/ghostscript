<?php

namespace Ordinary9843\Exceptions;

use Exception;

class BaseException extends Exception
{
    /** @var int */
    const CODE_DEFAULT = 0;

    /** @var array */
    protected $detail = [];

    /**
     * @param string $message
     * @param int $code
     * @param array $detail
     * @param BaseException $previous
     */
    public function __construct(string $message = 'An unexpected error occurred.', int $code = self::CODE_DEFAULT, array $detail = [], BaseException $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->detail = $detail;
    }

    /**
     * @return array
     */
    public function getDetail(): array
    {
        return $this->detail;
    }
}
