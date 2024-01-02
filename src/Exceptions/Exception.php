<?php

namespace Ordinary9843\Exceptions;

use Exception as BaseException;

class Exception extends BaseException
{
    /** @var array */
    protected $detail = [];

    /**
     * @param string $message
     * @param int $code
     * @param BaseException $previous
     */
    public function __construct(string $message = '', int $code = 0, BaseException $previous = null, array $detail = [])
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
