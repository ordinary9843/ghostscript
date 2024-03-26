<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Exceptions\BaseException;
use Ordinary9843\Exceptions\HandlerException;
use Ordinary9843\Exceptions\InvalidException;
use Ordinary9843\Interfaces\HandlerInterface;

class GuessHandler extends BaseHandler implements HandlerInterface
{
    /** @var array */
    protected $argumentsMapping = ['file'];

    /**
     * @param array ...$arguments
     * 
     * @return float
     * 
     * @throws InvalidException
     */
    public function execute(...$arguments): float
    {
        $this->mapArguments($arguments);

        try {
            $file = $arguments['file'];
            if (!$this->isFile($file)) {
                throw new InvalidException('Failed to convert, "' . $file . '" is not exist.', InvalidException::CODE_FILE_TYPE);
            }

            $fo = @fopen($file, 'rb');
            fseek($fo, 0);
            preg_match('/%PDF-(\d\.\d)/', fread($fo, 1024), $match);
            fclose($fo);

            return (float)($match[1] ?? 0);
        } catch (BaseException $exception) {
            throw new HandlerException($exception->getMessage(), HandlerException::CODE_EXECUTE, [
                'arguments' => $arguments
            ], $exception);
        }
    }
}
