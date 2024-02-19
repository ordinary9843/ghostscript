<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Helpers\PathHelper;
use Ordinary9843\Exceptions\Exception;
use Ordinary9843\Constants\MessageConstant;
use Ordinary9843\Constants\ToImageConstant;
use Ordinary9843\Exceptions\ExecuteException;
use Ordinary9843\Interfaces\HandlerInterface;
use Ordinary9843\Constants\GhostscriptConstant;
use Ordinary9843\Exceptions\InvalidFilePathException;

class ToImageHandler extends Handler implements HandlerInterface
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

        $images = [];

        try {
            $file = PathHelper::convertPathSeparator($arguments[0] ?? '');
            $path = PathHelper::convertPathSeparator($arguments[1] ?? '');
            $type = PathHelper::convertPathSeparator($arguments[2] ?? ToImageConstant::TYPE_JPEG);
            $totalPage = $this->getPdfTotalPage($file);
            if ($totalPage < 1) {
                throw new ExecuteException('Failed to read the total number of pages in "' . $file . '".');
            }

            (!$this->getFileSystem()->isDir($path)) && mkdir($path, 0755);
            $imageFormatPath = ($totalPage > 1) ? '/image_%d.' . $type : '/' . pathinfo($file, PATHINFO_FILENAME) . '.' . $type;
            $output = shell_exec($this->optionsToCommand($this->getBinPath() . ' -dQUIET -dNOPAUSE -dBATCH -sDEVICE=' . ToImageConstant::TYPE_JPEG . ' -r300 -sOutputFile=' . escapeshellarg(PathHelper::convertPathSeparator($path . $imageFormatPath)) . ' ' . escapeshellarg(PathHelper::convertPathSeparator($this->convertToTmpFile($file)))));
            if ($output) {
                throw new ExecuteException('Failed to convert "' . $file . '", because ' . $output . '.');
            }

            $images = array_map(function ($i) use ($path, $imageFormatPath) {
                return $path . sprintf($imageFormatPath, $i + 1);
            }, range(0, $totalPage - 1));
        } catch (Exception $e) {
            $this->addMessage(MessageConstant::TYPE_ERROR, $e->getMessage());

            $images = [];
        } finally {
            $this->clearTmpFiles();
        }

        return $images;
    }
}
