<?php

namespace FlorianEc\DownloadSite\Tests;

use FlorianEc\DownloadSite\Page;
use Mockery;

/**
 * PageTest
 *
 * @package   FlorianEc\DownloadSite\Tests
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2014 Florian Eckerstorfer
 * @group     unit
 */
class PageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Page
     */
    private $page;

    /**
     * @var \FlorianEc\DownloadSite\Site|Mockery\MockInterface
     */
    private $site;

    public function setUp()
    {
        $this->site = Mockery::mock('FlorianEc\DownloadSite\Site');
        $this->site->shouldReceive('getRootUrl')->andReturn('https://florian.ec/');

        $this->page = Page::createFromSite($this->site);
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\Page::createFromSite()
     * @covers FlorianEc\DownloadSite\Page::__construct()
     * @covers FlorianEc\DownloadSite\Page::getUrl()
     * @covers FlorianEc\DownloadSite\Page::getSite()
     */
    public function createFromSiteCreatesPageForRootUrl()
    {
        /** @var \FlorianEc\DownloadSite\Site|\Mockery\MockInterface $site */
        $site = Mockery::mock('FlorianEc\DownloadSite\Site');
        $site->shouldReceive('getRootUrl')->andReturn('https://florian.ec/');

        $page = Page::createFromSite($site);

        $this->assertSame($site, $page->getSite());
        $this->assertEquals('https://florian.ec/', $page->getUrl()->__toString());
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\Page::createFromLink()
     * @covers FlorianEc\DownloadSite\Page::__construct()
     * @covers FlorianEc\DownloadSite\Page::getUrl()
     * @covers FlorianEc\DownloadSite\Page::getSite()
     */
    public function createFromLinkCreatesPageFromRootLinkAndSite()
    {
        /** @var \FlorianEc\DownloadSite\Site|\Mockery\MockInterface $site */
        $site = Mockery::mock('FlorianEc\DownloadSite\Site');
        $site->shouldReceive('getBaseUrl')->andReturn('https://florian.ec/');

        /** @var \FlorianEc\DownloadSite\Page $parent */
        $parent = Mockery::mock('FlorianEc\DownloadSite\Page');

        $page = Page::createFromLink('/articles', $parent, $site);

        $this->assertSame($site, $page->getSite());
        $this->assertEquals('https://florian.ec/articles', $page->getUrl()->__toString());
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\Page::createFromLink()
     * @covers FlorianEc\DownloadSite\Page::__construct()
     * @covers FlorianEc\DownloadSite\Page::getUrl()
     * @covers FlorianEc\DownloadSite\Page::getSite()
     */
    public function createFromLinkCreatesPageFromRelativeLinkAndSite()
    {
        /** @var \FlorianEc\DownloadSite\Site|\Mockery\MockInterface $site */
        $site = Mockery::mock('FlorianEc\DownloadSite\Site');
        $site->shouldReceive('getBaseUrl')->andReturn('https://florian.ec/');

        $url = Mockery::mock('League\Url\UrlInterface');
        $url->shouldReceive('__toString')->andReturn('https://florian.ec/assets/img1/bar.jpg');
        $url->shouldReceive('getPath')->andReturn('assets/img1/bar.jpg');
        $url->shouldReceive('getScheme')->andReturn('https');
        $url->shouldReceive('getHost')->andReturn('florian.ec');

        /** @var \FlorianEc\DownloadSite\Page|Mockery\MockInterface $parent */
        $parent = Mockery::mock('FlorianEc\DownloadSite\Page');
        $parent->shouldReceive('getUrl')->andReturn($url);

        $page = Page::createFromLink('../foo.jpg', $parent, $site);

        $this->assertSame($site, $page->getSite());
        $this->assertEquals('https://florian.ec/assets/foo.jpg', $page->getUrl()->__toString());
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\Page::createFromLink()
     * @covers FlorianEc\DownloadSite\Page::__construct()
     * @covers FlorianEc\DownloadSite\Page::getUrl()
     * @covers FlorianEc\DownloadSite\Page::getSite()
     */
    public function createFromLinkRemovesAnchorsFromLink()
    {
        /** @var \FlorianEc\DownloadSite\Site|\Mockery\MockInterface $site */
        $site = Mockery::mock('FlorianEc\DownloadSite\Site');
        $site->shouldReceive('getBaseUrl')->andReturn('https://florian.ec/');

        /** @var \FlorianEc\DownloadSite\Page $parent */
        $parent = Mockery::mock('FlorianEc\DownloadSite\Page');

        $page = Page::createFromLink('/articles#sub-section', $parent, $site);

        $this->assertSame($site, $page->getSite());
        $this->assertEquals('https://florian.ec/articles', $page->getUrl()->__toString());
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\Page::addLink()
     * @covers FlorianEc\DownloadSite\Page::getLinks()
     */
    public function addLinkAddsALinkToThePage()
    {
        $link = Mockery::mock('FlorianEc\DownloadSite\Page');
        $this->site->shouldReceive('addPage')->with($link);
        $this->page->addLink($link);

        $this->assertContains($link, $this->page->getLinks());
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\Page::isDownloaded()
     */
    public function isDownloadedReturnsFalseIfPageIsNotDownloaded()
    {
        $this->assertFalse($this->page->isDownloaded());
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\Page::download()
     * @covers FlorianEc\DownloadSite\Page::getContent()
     */
    public function downloadSetsPageAsDownloadedAndSetsContent()
    {
        $this->page->download('hello');

        $this->assertTrue($this->page->isDownloaded());
        $this->assertEquals('hello', $this->page->getContent());
    }
}
