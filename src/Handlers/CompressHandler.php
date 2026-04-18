<?php

declare(strict_types=1);

namespace Ordinary9843\Handlers;

use Ordinary9843\Helpers\PathHelper;
use Ordinary9843\Exceptions\BaseException;
use Ordinary9843\Exceptions\HandlerException;
use Ordinary9843\Exceptions\InvalidException;
use Ordinary9843\Interfaces\HandlerInterface;
use Ordinary9843\Exceptions\NotFoundException;
use Ordinary9843\Constants\CompressConstant;

class CompressHandler extends BaseHandler implements HandlerInterface
{
    /** @var array */
    protected $argumentsMapping = ['file', 'quality'];

    /** @var string[] */
    private const ALLOWED_QUALITIES = [
        CompressConstant::SCREEN,
        CompressConstant::EBOOK,
        CompressConstant::PRINTER,
        CompressConstant::PREPRESS,
    ];

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
            $quality = $arguments['quality'] ?? CompressConstant::EBOOK;

            if (!$this->isFile($file)) {
                throw new NotFoundException('"' . $file . '" does not exist.', NotFoundException::CODE_FILE);
            } elseif (!$this->isPdf($file)) {
                throw new InvalidException('"' . $file . '" is not a PDF.', InvalidException::CODE_FILE_TYPE);
            }

            if (!in_array($quality, self::ALLOWED_QUALITIES, true)) {
                throw new InvalidException(
                    'Invalid quality "' . $quality . '". Allowed: ' . implode(', ', self::ALLOWED_QUALITIES) . '.',
                    InvalidException::CODE_FILE_TYPE
                );
            }

            $tmpFile = $this->getTmpFile();
            $output = shell_exec(
                $this->optionsToCommand(
                    sprintf(
                        '%s -sDEVICE=pdfwrite -dQUIET -dNOPAUSE -dBATCH -dCompatibilityLevel=1.4 -dPDFSETTINGS=/%s -sOutputFile=%s %s',
                        $this->getBinPath(),
                        $quality,
                        escapeshellarg($tmpFile),
                        escapeshellarg($this->convertToTmpFile($file))
                    )
                )
            );
            if ($output) {
                throw new HandlerException('Failed to compress file "' . $file . '", because ' . $output . '.', HandlerException::CODE_EXECUTE);
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
