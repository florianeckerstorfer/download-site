<?php

namespace FlorianEc\DownloadSite\Util;

use League\Url\UrlInterface;

/**
 * UrlUtil
 *
 * @package   FlorianEc\DownloadSite
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2014 Florian Eckerstorfer
 */
class UrlUtil
{
    /**
     * @param UrlInterface $url
     *
     * @return string
     */
    public static function removeFilename(UrlInterface $url)
    {
        $path = '/'.$url->getPath();
        $possibleFilename = substr($path, strrpos($path, '/'));
        if (strpos($possibleFilename, '.') !== false) {
            $path = substr($path, 0, strrpos($path, '/')+1);
        }

        return $url->getScheme().'://'.$url->getHost().$path;
    }

    /**
     * @param string $url
     * @param UrlInterface $reference
     *
     * @return string
     */
    public static function getAbsoluteUrl($url, UrlInterface $reference)
    {
        $baseUrl = rtrim(self::removeFilename($reference), '/');
        while (preg_match('/^\.\.\//', $url) === 1) {
            $url = substr($url, 3);
            $baseUrl = rtrim(substr($baseUrl, 0, strrpos($baseUrl, '/')), '/');
        }
        $url = preg_replace('/^(\.\/?)/', '', $url);

        return $baseUrl.'/'.$url;
    }

    private function __construct() {}
}
