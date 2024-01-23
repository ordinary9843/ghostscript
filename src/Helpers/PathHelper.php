<?php

namespace Ordinary9843\Helpers;

class PathHelper
{
    /**
     * @param string $path
     *
     * @return string
     */
    public static function convertPathSeparator(string $path): string
    {
        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }
}
