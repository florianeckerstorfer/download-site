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
     * @param Site   $site
     *
     * @return Page
     */
    public static function createFromLink($link, Site $site)
    {
        if (strpos($link, '#') > -1) {
            $link = substr($link, 0, strpos($link, '#'));
        }

        return new self(UrlImmutable::createFromUrl(sprintf('%s%s', $site->getBaseUrl(), ltrim($link, '/'))), $site);
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
