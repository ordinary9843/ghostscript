<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Helpers\PathHelper;
use Ordinary9843\Handlers\GuessHandler;
use Ordinary9843\Handlers\ConvertHandler;
use Ordinary9843\Exceptions\BaseException;
use Ordinary9843\Exceptions\HandlerException;
use Ordinary9843\Exceptions\InvalidException;
use Ordinary9843\Interfaces\HandlerInterface;
use Ordinary9843\Constants\GhostscriptConstant;

class MergeHandler extends BaseHandler implements HandlerInterface
{
    /** @var array */
    protected $argumentsMapping = ['file', 'files', 'isAutoConvert'];

    /** @var ConvertHandler */
    protected $convertHandler = null;

    /** @var GuessHandler */
    protected $guessHandler = null;

    public function __construct()
    {
        $this->convertHandler = new ConvertHandler();
        $this->guessHandler = new GuessHandler();
    }

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
            $files = $arguments['files'];
            $isAutoConvert = (bool)$arguments['isAutoConvert'];
            $files = array_filter($files, function ($value) use ($isAutoConvert) {
                $value = PathHelper::convertPathSeparator($value);
                if (!$this->isFile($value) || !$this->isPdf($value)) {
                    return false;
                }

                ($isAutoConvert === true && $this->guessHandler->execute($value) !== GhostscriptConstant::STABLE_VERSION) && $value = $this->convertHandler->execute($value, GhostscriptConstant::STABLE_VERSION);

                return true;
            });
            $output = shell_exec(
                $this->optionsToCommand(
                    sprintf(
                        '%s -sDEVICE=pdfwrite -dQUIET -dNOPAUSE -dBATCH -sOUTPUTFILE=%s %s',
                        $this->getBinPath(),
                        escapeshellarg($file),
                        implode(' ', array_map(function ($value) {
                            return escapeshellarg($this->convertToTmpFile($value));
                        }, $files))
                    )
                )
            );
            if ($output) {
                throw new HandlerException('Failed to merge "' . $file . '", because ' . $output . '.', HandlerException::CODE_EXECUTE);
            }

            return $file;
        } catch (BaseException $exception) {
            $this->delete($file);

            throw new HandlerException($exception->getMessage(), HandlerException::CODE_EXECUTE, [
                'arguments' => $arguments
            ], $exception);
        } finally {
            $this->clearTmpFiles();
        }
    }
}
