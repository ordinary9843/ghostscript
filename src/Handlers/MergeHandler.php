<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Configs\Config;
use Ordinary9843\Helpers\PathHelper;
use Ordinary9843\Exceptions\Exception;
use Ordinary9843\Handlers\GuessHandler;
use Ordinary9843\Handlers\ConvertHandler;
use Ordinary9843\Constants\MessageConstant;
use Ordinary9843\Exceptions\ExecuteException;
use Ordinary9843\Interfaces\HandlerInterface;
use Ordinary9843\Constants\GhostscriptConstant;
use Ordinary9843\Exceptions\InvalidFilePathException;

class MergeHandler extends Handler implements HandlerInterface
{
    /** @var ConvertHandler */
    private $convertHandler = null;

    /** @var GuessHandler */
    private $guessHandler = null;

    public function __construct(Config $config = null)
    {
        $this->convertHandler = new ConvertHandler($config);
        $this->guessHandler = new GuessHandler($config);
    }

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
            $files = $arguments[1] ?? [];
            $isAutoConvert = (bool)($arguments[2] ?? true);
            $files = array_filter($files, function ($value) use ($isAutoConvert) {
                $value = PathHelper::convertPathSeparator($value);
                if (!$this->getFileSystem()->isFile($value)) {
                    $this->addMessage(MessageConstant::TYPE_ERROR, $value . ' is not exist.');

                    return false;
                } elseif (!$this->isPdf($value)) {
                    $this->addMessage(MessageConstant::TYPE_ERROR, $value . ' is not PDF.');

                    return false;
                }
                ($isAutoConvert === true && $this->guessHandler->execute($value) !== GhostscriptConstant::STABLE_VERSION) && $value = $this->convertHandler->execute($value, GhostscriptConstant::STABLE_VERSION);

                return true;
            });
            $output = shell_exec(
                $this->optionsToCommand(
                    sprintf(
                        GhostscriptConstant::MERGE_COMMAND,
                        $this->getBinPath(),
                        escapeshellarg($file),
                        implode(' ', array_map(function ($value) {
                            return escapeshellarg($this->convertToTmpFile($value));
                        }, $files))
                    )
                )
            );
            if ($output) {
                throw new ExecuteException('Failed to merge ' . $file . ', because ' . $output);
            }
        } catch (Exception $e) {
            $this->getFileSystem()->delete($file);
            $this->addMessage(MessageConstant::TYPE_ERROR, $e->getMessage());

            $file = '';
        } finally {
            $this->clearTmpFiles();
        }

        return $file;
    }
}
