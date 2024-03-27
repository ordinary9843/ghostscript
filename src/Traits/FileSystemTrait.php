<?php

namespace Ordinary9843\Traits;

trait FileSystemTrait
{
    /**
     * @param string $path
     *
     * @return bool
     */
    public function isValid(string $path): bool
    {
        return ($path && ($this->isDir($path) || $this->isFile($path)));
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function isDir(string $path): bool
    {
        return ($path && is_dir($path));
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function isFile(string $path): bool
    {
        return ($path && is_file($path));
    }

    /**
     * @param string $path
     *
     * @return void
     */
    public function delete(string $path): void
    {
        if ($this->isFile($path)) {
            @unlink($path);
        } elseif ($this->isDir($path)) {
            @rmdir($path);
        }
    }

    /**
     * @param string $path
     * @param int $permission
     *
     * @return void
     */
    public function makeDir(string $path, int $permission = 0755): void
    {
        @mkdir($path, $permission);
    }
}
