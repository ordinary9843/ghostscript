<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Exceptions\BaseException;
use Ordinary9843\Constants\MessageConstant;
use Ordinary9843\Exceptions\HandlerException;
use Ordinary9843\Exceptions\InvalidException;
use Ordinary9843\Interfaces\HandlerInterface;

class GuessHandler extends Handler implements HandlerInterface
{
    /**
     * @param array ...$arguments
     * 
     * @return float
     * 
     * @throws HandlerException
     */
    public function execute(...$arguments): float
    {
        try {
            $file = $arguments[0] ?? '';
            if (!$this->getFileSystem()->isFile($file)) {
                throw new InvalidException('Failed to convert, "' . $file . '" is not exist.', InvalidException::CODE_FILE_TYPE);
            }

            $fo = @fopen($file, 'rb');
            fseek($fo, 0);
            preg_match('/%PDF-(\d\.\d)/', fread($fo, 1024), $match);
            fclose($fo);

            return (float)($match[1] ?? 0);
        } catch (BaseException $e) {
            $this->addMessage(MessageConstant::TYPE_ERROR, $e->getMessage());

            return 0;
        }
    }
}
