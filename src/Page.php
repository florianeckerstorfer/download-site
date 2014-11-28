<?php

namespace FlorianEc\DownloadSite;

use League\Url\UrlImmutable;
use League\Url\UrlInterface;

/**
 * Page
 *
 * @package   FlorianEc\DownloadSite
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2014 Florian Eckerstorfer
 */
class Page
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var Site
     */
    private $site;

    /**
     * @var Page[]
     */
    private $links;

    /**
     * @var string
     */
    private $content;

    /**
     * @var bool
     */
    private $downloaded = false;

    /**
     * @param Site $site
     *
     * @return Page
     */
    public static function createFromSite(Site $site)
    {
        return new self(UrlImmutable::createFromUrl($site->getRootUrl()), $site);
    }

    /**
     * @param string $link
     * @param Page   $parent
     * @param Site   $site
     *
     * @return Page
     */
    public static function createFromLink($link, Page $parent, Site $site)
    {
        if (strpos($link, '#') > -1) {
            $link = substr($link, 0, strpos($link, '#'));
        }
        if (preg_match('/^\//', $link) === 1) {
            $url = sprintf('%s%s', $site->getBaseUrl(), ltrim($link, '/'));
        } else if (preg_match('/^https?/', $link) !== 1) {
            $url = UrlUtil::getAbsoluteUrl($link, $parent->getUrl());
        } else {
            $url = $link;
        }

        return new self(UrlImmutable::createFromUrl($url), $site);
    }

    /**
     * @param UrlInterface $url
     * @param Site         $site;
     */
    private function __construct(UrlInterface $url, Site $site)
    {
        $this->url  = $url;
        $this->site = $site;
    }

    /**
     * @return UrlInterface
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param Page $link
     *
     * @return Page
     */
    public function addLink(Page $link)
    {
        $this->links[] = $link;
        $this->site->addPage($link);

        return $this;
    }

    /**
     * @return Page[]
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param string $content
     *
     * @return Page
     */
    public function download($content)
    {
        $this->content    = $content;
        $this->downloaded = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDownloaded()
    {
        return $this->downloaded;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}
