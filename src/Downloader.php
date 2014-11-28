<?php

namespace FlorianEc\DownloadSite;

use Buzz\Browser;
use Psr\Log\LoggerInterface;

/**
 * Download
 *
 * @package   FlorianEc\DownloadSite
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2014 Florian Eckerstorfer
 */
class Downloader
{
    /**
     * @var Browser
     */
    private $browser;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @param Browser $browser
     */
    public function __construct(Browser $browser)
    {
        $this->browser = $browser;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return Downloader
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param Site $site
     *
     * @return Site
     */
    public function download(Site $site)
    {
        $page = Page::createFromSite($site);
        $site->addPage($page);

        $this->downloadPage($page);

        while ($nextPage = $site->getFirstNotDownloadedPage()) {
            $this->downloadPage($nextPage);
        }
    }

    /**
     * @param Page $page
     *
     * @return void
     */
    public function downloadPage(Page $page)
    {
        $this->log('debug', sprintf('Downloading URL %s', $page->getUrl()));
        $response = $this->browser->get($page->getUrl());
        $page->download($response->getContent());
        $this->log('info', sprintf('Downloaded page: %s', $page->getUrl()));

        $links = array_merge(
            $this->getHrefLinks($page),
            $this->getSrcLinks($page),
            $this->getSrcsetLinks($page),
            $this->getCssSrcLinks($page)
            // TODO: Get image files from CSS
            // TODO: Get font files from CSS
        );

        foreach ($this->filterLinks($links, $page->getSite()) as $link) {
            $linkPage = Page::createFromLink($link, $page, $page->getSite());
            $this->log('debug', sprintf('Found link: %s', $linkPage->getUrl()));
            $page->addLink($linkPage);
        }
    }

    /**
     * @param string[] $links
     * @param Site     $site
     *
     * @return string[]
     */
    protected function filterLinks(array $links, Site $site)
    {
        return array_filter($links, function ($link) use ($site) {
            $baseUrl = $site->getBaseUrl()->__toString();
            $baseUrl = str_replace(['/', '.', '?'], ['\/', '\.', '\?'], $baseUrl);
            if (preg_match(sprintf('/^%s/', $baseUrl), $link) === 1) {
                return true;
            }

            return preg_match('/^(https?|mailto|ftp|data|\/\/|[^a-zA-Z0-9.-_])/', $link) === 0;
        });
    }

    /**
     * @param Page $page
     *
     * @return string[]
     */
    protected function getHrefLinks(Page $page)
    {
        if (preg_match_all('/href="(.*?)"/', $page->getContent(), $matches) && isset($matches[1])) {
            return $matches[1];
        }

        return [];
    }

    /**
     * @param Page $page
     *
     * @return string[]
     */
    protected function getSrcLinks(Page $page)
    {
        if (preg_match_all('/src="(.*?)"/', $page->getContent(), $matches) && isset($matches[1])) {
            return $matches[1];
        }

        return [];
    }

    /**
     * @param Page $page
     *
     * @return string[]
     */
    protected function getSrcsetLinks(Page $page)
    {
        $links = [];

        if (preg_match_all('/srcset="(.*?)"/', $page->getContent(), $matches) && isset($matches[1])) {
            foreach ($matches[1] as $match) {
                $parts = array_map('trim', explode(',', $match));
                foreach ($parts as $part) {
                    $links[] = explode(' ', $part)[0];
                }
            }
        }

        return $links;
    }

    /**
     * @param Page $page
     *
     * @return string[]
     */
    protected function getCssSrcLinks(Page $page)
    {
        if (preg_match('/\.css$/', $page->getUrl()) !== 1) {
            return [];
        }

        if (preg_match_all('/url\((.*?)\)/', $page->getContent(), $matches) && isset($matches[1])) {
            return $matches[1];
        }
    }

    /**
     * @param string $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    protected function log($level, $message, array $context = [])
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }
}
