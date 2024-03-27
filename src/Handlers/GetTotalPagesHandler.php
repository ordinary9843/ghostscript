<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Helpers\PathHelper;
use Ordinary9843\Exceptions\BaseException;
use Ordinary9843\Exceptions\HandlerException;
use Ordinary9843\Exceptions\InvalidException;
use Ordinary9843\Interfaces\HandlerInterface;

class GetTotalPagesHandler extends BaseHandler implements HandlerInterface
{
    /** @var array */
    protected $argumentsMapping = ['file'];

    /**
     * @param array ...$arguments
     * 
     * @return int
     * 
     * @throws HandlerException
     * @throws InvalidException
     */
    public function execute(...$arguments): int
    {
        $this->validateBinPath();
        $this->mapArguments($arguments);

        echo PHP_EOL . 'GetTotalPages: ' . PHP_EOL;
        print_r($arguments);
        try {
            $file = PathHelper::convertPathSeparator($arguments['file']);
            if (!$this->isFile($file)) {
                throw new InvalidException('"' . $file . '" is not exist.', InvalidException::CODE_FILEPATH);
            } elseif (!$this->isPdf($file)) {
                throw new InvalidException('"' . $file . '" is not PDF.', InvalidException::CODE_FILE_TYPE);
            }

            $output = shell_exec(
                sprintf(
                    '%s -dQUIET -dNODISPLAY -dNOSAFER -c "(%s) (r) file runpdfbegin pdfpagecount = quit"',
                    $this->getBinPath(),
                    $file
                )
            );

            return ($output) ? (int)$output : 0;
        } catch (BaseException $exception) {
            throw new HandlerException($exception->getMessage(), HandlerException::CODE_EXECUTE, [
                'file' => $file
            ], $exception);
        }
    }
}
