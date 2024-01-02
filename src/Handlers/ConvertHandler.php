<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Helpers\PathHelper;
use Ordinary9843\Exceptions\Exception;
use Ordinary9843\Constants\MessageConstant;
use Ordinary9843\Exceptions\ExecuteException;
use Ordinary9843\Interfaces\HandlerInterface;
use Ordinary9843\Constants\GhostscriptConstant;
use Ordinary9843\Exceptions\InvalidFilePathException;

class ConvertHandler extends Handler implements HandlerInterface
{
    /**
     * @param array ...$arguments
     * 
     * @return string
     * 
     * @throws InvalidFilePathException
     */
    public function execute(...$arguments): string
    {
        $this->validateBinPath();

        try {
            $file = PathHelper::convertPathSeparator($arguments[0] ?? '');
            $version = $arguments[1] ?? 0;
            if (!$this->getFileSystem()->isFile($file)) {
                throw new ExecuteException($file . ' is not exist.');
            } elseif (!$this->isPdf($file)) {
                throw new ExecuteException($file . ' is not PDF.');
            }

            $tmpFile = $this->getTmpFile();
            $output = shell_exec(
                $this->optionsToCommand(
                    sprintf(
                        GhostscriptConstant::CONVERT_COMMAND,
                        $this->getBinPath(),
                        $version,
                        escapeshellarg($tmpFile),
                        escapeshellarg($this->convertToTmpFile($file))
                    )
                )
            );
            if ($output) {
                throw new ExecuteException('Failed to convert ' . $file . ', because ' . $output);
            }

            @copy($tmpFile, $file);
        } catch (Exception $e) {
            $this->addMessage(MessageConstant::TYPE_ERROR, $e->getMessage());

            $file = '';
        } finally {
            $this->clearTmpFiles();
        }

        return $file;
    }
}
