<?php

declare(strict_types=1);

namespace Ordinary9843\Factories;

use Ordinary9843\Handlers\BaseHandler;
use Ordinary9843\Handlers\CompressHandler;
use Ordinary9843\Handlers\ConvertHandler;
use Ordinary9843\Handlers\GetTotalPagesHandler;
use Ordinary9843\Handlers\GuessHandler;
use Ordinary9843\Handlers\MergeHandler;
use Ordinary9843\Handlers\SplitHandler;
use Ordinary9843\Handlers\ToImageHandler;
use Ordinary9843\Interfaces\HandlerInterface;
use Ordinary9843\Exceptions\NotFoundException;

class HandlerFactory
{
    private const HANDLER_MAP = [
        'base'          => BaseHandler::class,
        'compress'      => CompressHandler::class,
        'convert'       => ConvertHandler::class,
        'guess'         => GuessHandler::class,
        'getTotalPages' => GetTotalPagesHandler::class,
        'merge'         => MergeHandler::class,
        'split'         => SplitHandler::class,
        'toImage'       => ToImageHandler::class,
    ];

    /**
     * @param string $type
     *
     * @return HandlerInterface
     *
     * @throws NotFoundException
     */
    public function create(string $type): HandlerInterface
    {
        if (!isset(self::HANDLER_MAP[$type])) {
            throw new NotFoundException('Handler "' . $type . '" does not exist.', NotFoundException::CODE_CLASS);
        }

        $class = self::HANDLER_MAP[$type];

        return new $class();
    }
}
