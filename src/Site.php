<?php

namespace FlorianEc\DownloadSite;

use League\Url\UrlImmutable;
use League\Url\UrlInterface;

/**
 * Site
 *
 * @package   FlorianEc\DownloadSite
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2014 Florian Eckerstorfer
 */
class Site
{
    /**
     * @var UrlInterface
     */
    private $rootUrl;

    /**
     * @var UrlInterface
     */
    private $baseUrl;

    /**
     * @var Page[]
     */
    private $pages = [];

    /**
     * @param UrlInterface $rootUrl
     */
    public function __construct(UrlInterface $rootUrl)
    {
        $this->rootUrl = $rootUrl;
        $this->baseUrl = UrlImmutable::createFromUrl($rootUrl->getBaseUrl());

    }

    /**
     * @return UrlInterface
     */
    public function getRootUrl()
    {
        return $this->rootUrl;
    }

    /**
     * @return UrlInterface
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param Page $page
     *
     * @return Site
     */
    public function addPage(Page $page)
    {
        if ($this->hasPage($page) === false) {
            $this->pages[] = $page;
        }

        return $this;
    }

    /**
     * @param Page $newPage
     *
     * @return bool
     */
    public function hasPage(Page $newPage)
    {
        foreach ($this->pages as $page) {
            if ($page->getUrl()->sameValueAs($newPage->getUrl())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the next page that is not yet downloaded, returns `null` when all pages are downloaded.
     *
     * @return Page|null
     */
    public function getFirstNotDownloadedPage()
    {
        foreach ($this->pages as $page) {
            if ($page->isDownloaded() === false) {
                return $page;
            }
        }

        return null;
    }

    /**
     * @return Page[]
     */
    public function getPages()
    {
        return $this->pages;
    }
}
