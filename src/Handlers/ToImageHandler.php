<?php

namespace Ordinary9843\Handlers;

use Ordinary9843\Helpers\PathHelper;
use Ordinary9843\Exceptions\BaseException;
use Ordinary9843\Constants\ToImageConstant;
use Ordinary9843\Exceptions\HandlerException;
use Ordinary9843\Exceptions\InvalidException;
use Ordinary9843\Interfaces\HandlerInterface;
use Ordinary9843\Handlers\GetTotalPagesHandler;

class ToImageHandler extends BaseHandler implements HandlerInterface
{
    /** @var array */
    protected $argumentsMapping = ['file', 'path', 'type'];

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

        echo PHP_EOL . 'ToImage: ' . PHP_EOL;
        print_r($arguments);
        try {
            $file = PathHelper::convertPathSeparator($arguments['file']);
            $path = PathHelper::convertPathSeparator($arguments['path']);
            $type = PathHelper::convertPathSeparator($arguments['type'] ?? ToImageConstant::TYPE_JPEG);
            $totalPages = $this->getTotalPagesHandler->execute($file);
            (!$this->isDir($path)) && $this->makeDir($path);
            $imageFormatPath = ($totalPages > 1) ? '/image_%d.' . $type : '/' . pathinfo($file, PATHINFO_FILENAME) . '.' . $type;
            $output = shell_exec($this->optionsToCommand($this->getBinPath() . ' -dQUIET -dNOPAUSE -dBATCH -sDEVICE=' . ToImageConstant::TYPE_JPEG . ' -r300 -sOutputFile=' . escapeshellarg(PathHelper::convertPathSeparator($path . $imageFormatPath)) . ' ' . escapeshellarg(PathHelper::convertPathSeparator($this->convertToTmpFile($file)))));
            if ($output) {
                throw new HandlerException('Failed to convert file "' . $file . '" to image, because ' . $output . '.', HandlerException::CODE_EXECUTE);
            }

            return array_map(function ($i) use ($path, $imageFormatPath) {
                return $path . sprintf($imageFormatPath, $i + 1);
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
