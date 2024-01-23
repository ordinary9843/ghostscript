<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Helpers\PathHelper;
use Ordinary9843\Exceptions\Exception;
use Ordinary9843\Constants\SplitConstant;
use Ordinary9843\Constants\MessageConstant;
use Ordinary9843\Exceptions\ExecuteException;
use Ordinary9843\Interfaces\HandlerInterface;
use Ordinary9843\Exceptions\InvalidFilePathException;

class SplitHandler extends Handler implements HandlerInterface
{
    /**
     * @param array ...$arguments
     * 
     * @return array
     * 
     * @throws ExecuteException
     * @throws InvalidFilePathException
     */
    public function execute(...$arguments): array
    {
        $this->validateBinPath();

        $parts = [];

        try {
            $file = PathHelper::convertPathSeparator($arguments[0] ?? '');
            $path = $arguments[1] ?? '';
            $totalPage = $this->getPdfTotalPage($file);
            if ($totalPage < 1) {
                throw new ExecuteException('Failed to read the total number of pages in "' . $file . '".');
            }

            (!$this->getFileSystem()->isDir($path)) && mkdir($path, 0755);
            $pdfFormatPath = '/part_%d.pdf';
            $output = shell_exec(
                $this->optionsToCommand(
                    sprintf(
                        SplitConstant::COMMAND,
                        $this->getBinPath(),
                        1,
                        $totalPage,
                        escapeshellarg(PathHelper::convertPathSeparator($path . $pdfFormatPath)),
                        escapeshellarg($this->convertToTmpFile($file))
                    )
                )
            );
            if ($output) {
                throw new ExecuteException('Failed to merge "' . $file . '", because ' . $output . '.');
            }

            $parts = array_map(function ($i) use ($path, $pdfFormatPath) {
                return $path . sprintf($pdfFormatPath, $i);
            }, range(0, $totalPage - 1));
        } catch (Exception $e) {
            $this->addMessage(MessageConstant::TYPE_ERROR, $e->getMessage());

            $parts = [];
        } finally {
            $this->clearTmpFiles();
        }

        return $parts;
    }
}
