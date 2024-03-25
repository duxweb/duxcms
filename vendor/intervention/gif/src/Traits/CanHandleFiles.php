<?php

declare(strict_types=1);

namespace Intervention\Gif\Traits;

trait CanHandleFiles
{
     /**
     * Determines if input is file path
     *
     * @return bool
     */
    private static function isFilePath($input): bool
    {
        return is_string($input) && !self::hasNullBytes($input) && @is_file($input);
    }

    /**
     * Determine if given string contains null bytes
     *
     * @param string $string
     * @return bool
     */
    private static function hasNullBytes($string): bool
    {
        return strpos($string, chr(0)) !== false;
    }

    /**
     * Create file pointer from given gif image data
     *
     * @param string $data
     * @return resource
     */
    private static function getHandleFromData($data)
    {
        $handle = fopen('php://memory', 'r+');
        fwrite($handle, $data);
        rewind($handle);

        return $handle;
    }

    /**
     * Create file pounter from given file path
     *
     * @param string $path
     * @return resource
     */
    private static function getHandleFromFilePath(string $path)
    {
        return fopen($path, 'rb');
    }
}
