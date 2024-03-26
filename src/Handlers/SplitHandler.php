<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Helpers\PathHelper;
use Ordinary9843\Exceptions\BaseException;
use Ordinary9843\Exceptions\HandlerException;
use Ordinary9843\Exceptions\InvalidException;
use Ordinary9843\Interfaces\HandlerInterface;

class SplitHandler extends BaseHandler implements HandlerInterface
{
    /** @var array */
    protected $argumentsMapping = ['file', 'path'];

    /**
     * @param array ...$arguments
     * 
     * @return array
     * 
     * @throws HandlerException
     * @throws InvalidException
     */
    public function execute(...$arguments): array
    {
        $this->validateBinPath();
        $this->mapArguments($arguments);


        try {
            $file = PathHelper::convertPathSeparator($arguments['file']);
            $path = $arguments['path'];
            $totalPage = $this->getPdfTotalPage($file);
            if ($totalPage < 1) {
                throw new HandlerException('Failed to read the total number of pages in "' . $file . '".', HandlerException::CODE_EXECUTE);
            }

            (!$this->isDir($path)) && mkdir($path, 0755);
            $pdfFormatPath = '/part_%d.pdf';
            $output = shell_exec($this->optionsToCommand($this->getBinPath() . ' -sDEVICE=pdfwrite -dQUIET -dNOPAUSE -dBATCH -dSAFER -dFirstPage=1 -dLastPage=' . $totalPage . ' -sOutputFile=' . escapeshellarg(PathHelper::convertPathSeparator($path . $pdfFormatPath)) . ' ' . escapeshellarg(PathHelper::convertPathSeparator($this->convertToTmpFile($file)))));
            if ($output) {
                throw new HandlerException('Failed to merge "' . $file . '", because ' . $output . '.', HandlerException::CODE_EXECUTE);
            }

            return array_map(function ($i) use ($path, $pdfFormatPath) {
                return $path . sprintf($pdfFormatPath, $i);
            }, range(0, $totalPage - 1));
        } catch (BaseException $exception) {
            throw new HandlerException($exception->getMessage(), HandlerException::CODE_EXECUTE, [
                'arguments' => $arguments
            ], $exception);
        } finally {
            $this->clearTmpFiles();
        }
    }
}
