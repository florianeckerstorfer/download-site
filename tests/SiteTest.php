<?php

namespace FlorianEc\DownloadSite\Tests;

use FlorianEc\DownloadSite\Site;
use League\Url\UrlImmutable;
use Mockery;

/**
 * SiteTest
 *
 * @package   FlorianEc\DownloadSite\Tests
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2014 Florian Eckerstorfer
 * @group     unit
 */
class SiteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Site
     */
    private $site;

    public function setUp()
    {
        /** @var \League\Url\UrlInterface|\Mockery\MockInterface $url */
        $url = Mockery::mock('League\Url\UrlInterface');
        $url->shouldReceive('getBaseUrl')->andReturnNull();

        $this->site = new Site($url);
    }
    /**
     * @test
     * @covers FlorianEc\DownloadSite\Site::__construct()
     * @covers FlorianEc\DownloadSite\Site::getRootUrl()
     */
    public function constructorTakesRootUrlAndGetRootUrlReturnsIt()
    {
        $site = new Site(UrlImmutable::createFromUrl('https://florian.ec'));
        $this->assertEquals('https://florian.ec/', $site->getRootUrl()->__toString());
    }
    /**
     * @test
     * @covers FlorianEc\DownloadSite\Site::__construct()
     * @covers FlorianEc\DownloadSite\Site::getBaseUrl()
     */
    public function constructorTakesRootUrlAndGetBaseUrlReturnsBaseUrl()
    {
        $site = new Site(UrlImmutable::createFromUrl('https://florian.ec/articles'));
        $this->assertEquals('https://florian.ec/', $site->getBaseUrl()->__toString());
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\Site::addPage()
     * @covers FlorianEc\DownloadSite\Site::getPages()
     */
    public function addPageAddsPageToTheSite()
    {
        $page = $this->getPageMock();
        $this->site->addPage($page);

        $this->assertContains($page, $this->site->getPages());
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\Site::hasPage()
     */
    public function hasPageReturnsFalseIfSiteHasNoPage()
    {
        $this->assertFalse($this->site->hasPage($this->getPageMock()));
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\Site::hasPage()
     */
    public function hasPageReturnsFalseIfSiteNotHasPage()
    {
        $url2 = Mockery::mock('League\Url\UrlInterface');
        $url1 = Mockery::mock('League\Url\UrlInterface');
        $url1->shouldReceive('sameValueAs')->with($url2)->andReturn(false);

        $page1 = $this->getPageMock($url1);
        $page2 = $this->getPageMock($url2);
        $this->site->addPage($page1);

        $this->assertFalse($this->site->hasPage($page2));
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\Site::hasPage()
     */
    public function hasPageReturnsTrueIfSiteHasPage()
    {
        $url2 = Mockery::mock('League\Url\UrlInterface');
        $url1 = Mockery::mock('League\Url\UrlInterface');
        $url1->shouldReceive('sameValueAs')->with($url2)->andReturn(true);

        $page1 = $this->getPageMock($url1);
        $page2 = $this->getPageMock($url2);
        $this->site->addPage($page1);

        $this->assertTrue($this->site->hasPage($page2));
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\Site::getFirstNotDownloadedPage()
     */
    public function getFirstNotDownloadedPageReturnsFirstPageThatIsNotDownloaded()
    {
        $url2 = Mockery::mock('League\Url\UrlInterface');
        $url1 = Mockery::mock('League\Url\UrlInterface');
        $url1->shouldReceive('sameValueAs')->with($url2)->andReturn(false);

        $page1 = $this->getPageMock($url1, true);
        $page2 = $this->getPageMock($url2, false);
        $this->site->addPage($page1)
                   ->addPage($page2);

        $this->assertSame($page2, $this->site->getFirstNotDownloadedPage());
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\Site::getFirstNotDownloadedPage()
     */
    public function getFirstNotDownloadedPageReturnsNullIfEveryPageIsDownloaded()
    {
        $url2 = Mockery::mock('League\Url\UrlInterface');
        $url1 = Mockery::mock('League\Url\UrlInterface');
        $url1->shouldReceive('sameValueAs')->with($url2)->andReturn(false);

        $page1 = $this->getPageMock($url1, true);
        $page2 = $this->getPageMock($url2, true);
        $this->site->addPage($page1)
                   ->addPage($page2);

        $this->assertNull($this->site->getFirstNotDownloadedPage());
    }

    /**
     * @param \League\Url\UrlInterface|null $url
     * @param bool|null                     $downloaded
     *
     * @return \FlorianEc\DownloadSite\Page|Mockery\MockInterface
     */
    private function getPageMock($url = null, $downloaded = null)
    {
        $page = Mockery::mock('FlorianEc\DownloadSite\Page');
        if ($url !== null) {
            $page->shouldReceive('getUrl')->andReturn($url);
        }
        if ($downloaded !== null) {
            $page->shouldReceive('isDownloaded')->andReturn($downloaded);
        }

        return $page;
    }
}
