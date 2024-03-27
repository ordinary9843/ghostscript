<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Helpers\PathHelper;
use Ordinary9843\Exceptions\BaseException;
use Ordinary9843\Exceptions\HandlerException;
use Ordinary9843\Exceptions\InvalidException;
use Ordinary9843\Interfaces\HandlerInterface;
use Ordinary9843\Exceptions\NotFoundException;

class ConvertHandler extends BaseHandler implements HandlerInterface
{
    /** @var array */
    protected $argumentsMapping = ['file', 'version'];

    /**
     * @param array ...$arguments
     * 
     * @return string
     * 
     * @throws HandlerException
     * @throws InvalidException
     */
    public function execute(...$arguments): string
    {
        $this->validateBinPath();
        $this->mapArguments($arguments);

        try {
            $file = PathHelper::convertPathSeparator($arguments['file']);
            $version = $arguments['version'];
            if (!$this->isFile($file)) {
                throw new NotFoundException('"' . $file . '" is not exist.', NotFoundException::CODE_FILE);
            } elseif (!$this->isPdf($file)) {
                throw new InvalidException('"' . $file . '" is not PDF.', InvalidException::CODE_FILE_TYPE);
            }

            $tmpFile = $this->getTmpFile();
            $output = shell_exec(
                $this->optionsToCommand(
                    sprintf(
                        '%s -sDEVICE=pdfwrite -dQUIET -dNOPAUSE -dBATCH -dCompatibilityLevel=%s -sOutputFile=%s %s',
                        $this->getBinPath(),
                        $version,
                        escapeshellarg($tmpFile),
                        escapeshellarg($this->convertToTmpFile($file))
                    )
                )
            );
            if ($output) {
                throw new HandlerException('Failed to convert file "' . $file . '" version, because ' . $output . '.', HandlerException::CODE_EXECUTE);
            }

            @copy($tmpFile, $file);

            return $file;
        } catch (BaseException $exception) {
            throw new HandlerException($exception->getMessage(), HandlerException::CODE_EXECUTE, [
                'arguments' => $arguments
            ], $exception);
        } finally {
            $this->clearTmpFiles();
        }
    }
}
