<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Helpers\PathHelper;
use Ordinary9843\Exceptions\BaseException;
use Ordinary9843\Exceptions\HandlerException;
use Ordinary9843\Exceptions\InvalidException;
use Ordinary9843\Interfaces\HandlerInterface;
use Ordinary9843\Handlers\GetTotalPagesHandler;

class SplitHandler extends BaseHandler implements HandlerInterface
{
    /** @var array */
    protected $argumentsMapping = ['file', 'path'];

    /** @var GetTotalPagesHandler */
    protected $getTotalPagesHandler = null;

    public function __construct()
    {
        $this->getTotalPagesHandler = new GetTotalPagesHandler();
    }

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
            $totalPages = $this->getTotalPagesHandler->execute($file);
            (!$this->isDir($path)) && $this->makeDir($path);
            $pdfFormatPath = '/part_%d.pdf';
            $output = shell_exec($this->optionsToCommand($this->getBinPath() . ' -sDEVICE=pdfwrite -dQUIET -dNOPAUSE -dBATCH -dSAFER -dFirstPage=1 -dLastPage=' . $totalPages . ' -sOutputFile=' . escapeshellarg(PathHelper::convertPathSeparator($path . $pdfFormatPath)) . ' ' . escapeshellarg(PathHelper::convertPathSeparator($this->convertToTmpFile($file)))));
            if ($output) {
                throw new HandlerException('Failed to split file "' . $file . '", because ' . $output . '.', HandlerException::CODE_EXECUTE);
            }

            return array_map(function ($i) use ($path, $pdfFormatPath) {
                return $path . sprintf($pdfFormatPath, $i);
            }, range(0, $totalPages - 1));
        } catch (BaseException $exception) {
            throw new HandlerException($exception->getMessage(), HandlerException::CODE_EXECUTE, [
                'arguments' => $arguments
            ], $exception);
        } finally {
            $this->clearTmpFiles();
        }
    }
}
