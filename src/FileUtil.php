<?php

namespace FlorianEc\DownloadSite;

/**
 * FileUtil
 *
 * @package   FlorianEc\DownloadSite
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2014 Florian Eckerstorfer
 */
class FileUtil
{
    /**
     * @param string $filename
     *
     * @return bool
     */
    public static function hasExtension($filename)
    {
        $filename = basename($filename);
        if (strpos($filename, '.') === false) {
            return false;
        }
        $extension = substr($filename, strrpos($filename, '.')+1);

        return strlen($extension) > 0;
    }

    /**
     * Private constructor because all methods are static.
     *
     * @codeCoverageIgnore
     */
    private function __construct() {}
}
