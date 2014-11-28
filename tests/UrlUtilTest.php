<?php

namespace FlorianEc\DownloadSite\Tests;

use FlorianEc\DownloadSite\UrlUtil;
use League\Url\Url;

/**
 * UrlUtilTest
 *
 * @package   FlorianEc\DownloadSite\Tests
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2014 Florian Eckerstorfer
 * @group     unit
 */
class UrlUtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers FlorianEc\DownloadSite\UrlUtil::removeFilename()
     */
    public function removeFilenameRemovesFilenameFromUrl()
    {
        $this->assertEquals('http://florian.ec/', UrlUtil::removeFilename($this->getUrl('http://florian.ec/foo.jpg')));
        $this->assertEquals('http://florian.ec/', UrlUtil::removeFilename($this->getUrl('http://florian.ec/')));
        $this->assertEquals('http://florian.ec/', UrlUtil::removeFilename($this->getUrl('http://florian.ec')));
        $this->assertEquals(
            'http://florian.ec/foo/',
            UrlUtil::removeFilename($this->getUrl('http://florian.ec/foo/foo.jpg'))
        );
        $this->assertEquals('http://florian.ec/foo', UrlUtil::removeFilename($this->getUrl('http://florian.ec/foo')));
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\UrlUtil::getAbsoluteUrl()
     */
    public function getAbsoluteUrlReturnsAbsoluteUrl()
    {
        $this->assertEquals(
            'http://florian.ec/foo.jpg',
            UrlUtil::getAbsoluteUrl('foo.jpg', $this->getUrl('http://florian.ec'))
        );
        $this->assertEquals(
            'http://florian.ec/bar/foo.jpg',
            UrlUtil::getAbsoluteUrl('foo.jpg', $this->getUrl('http://florian.ec/bar'))
        );
        $this->assertEquals(
            'http://florian.ec/',
            UrlUtil::getAbsoluteUrl('.', $this->getUrl('http://florian.ec'))
        );
        $this->assertEquals(
            'http://florian.ec/foo/bar.css',
            UrlUtil::getAbsoluteUrl('./bar.css', $this->getUrl('http://florian.ec/foo/'))
        );
        $this->assertEquals(
            'http://florian.ec/bar.css',
            UrlUtil::getAbsoluteUrl('../bar.css', $this->getUrl('http://florian.ec/foo/'))
        );
        $this->assertEquals(
            'http://florian.ec/bar.css',
            UrlUtil::getAbsoluteUrl('../../../../bar.css', $this->getUrl('http://florian.ec/foo/bar/qoo/qoz/base.css'))
        );
    }

    protected function getUrl($url)
    {
        return Url::createFromUrl($url);
    }
}
