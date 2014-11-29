<?php

namespace FlorianEc\DownloadSite\Tests;

use FlorianEc\DownloadSite\Saver;
use Mockery;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\Filesystem\Filesystem;

/**
 * SaverTest
 *
 * @package   FlorianEc\DownloadSite\Tests
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2014 Florian Eckerstorfer
 * @group     unit
 */
class SaverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Saver
     */
    private $saver;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem|Mockery\MockInterface
     */
    private $filesystem;

    public function setUp()
    {
        vfsStream::setup('fixtures', null, ['dir' => []]);

        $this->filesystem = new Filesystem();
        $this->saver = new Saver($this->filesystem);
        $this->saver->setTargetDirectory(vfsStream::url('fixtures'));
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\Saver::__construct()
     * @covers FlorianEc\DownloadSite\Saver::setTargetDirectory()
     * @covers FlorianEc\DownloadSite\Saver::savePage()
     */
    public function savePageCreatesDirectoryAndSavesPage()
    {
        $url = Mockery::mock('League\Url\UrlInterface');
        $url->shouldReceive('getPath')->andReturn('pages/index.html');

        /** @var \FlorianEc\DownloadSite\Page|Mockery\MockInterface $page */
        $page = Mockery::mock('FlorianEc\DownloadSite\Page');
        $page->shouldReceive('getUrl')->andReturn($url);
        $page->shouldReceive('getContent')->andReturn('foobar');
        $page->shouldReceive('clear')->once();

        $this->saver->savePage($page);

        $this->assertEquals('foobar', file_get_contents(vfsStream::url('fixtures/pages/index.html')));
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\Saver::__construct()
     * @covers FlorianEc\DownloadSite\Saver::setTargetDirectory()
     * @covers FlorianEc\DownloadSite\Saver::savePage()
     */
    public function savePageAppendsFilenameAndSavesPage()
    {
        $url = Mockery::mock('League\Url\UrlInterface');
        $url->shouldReceive('getPath')->andReturn('pages/foo');

        /** @var \FlorianEc\DownloadSite\Page|Mockery\MockInterface $page */
        $page = Mockery::mock('FlorianEc\DownloadSite\Page');
        $page->shouldReceive('getUrl')->andReturn($url);
        $page->shouldReceive('getContent')->andReturn('foobar');
        $page->shouldReceive('clear')->once();

        $this->saver->savePage($page);

        $this->assertEquals('foobar', file_get_contents(vfsStream::url('fixtures/pages/foo/index.html')));
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\Saver::setLogger()
     * @covers FlorianEc\DownloadSite\Saver::log()
     */
    public function savePageUsesTheLogger()
    {
        $url = Mockery::mock('League\Url\UrlInterface');
        $url->shouldReceive('getPath')->andReturn('pages/foo');

        /** @var \FlorianEc\DownloadSite\Page|Mockery\MockInterface $page */
        $page = Mockery::mock('FlorianEc\DownloadSite\Page');
        $page->shouldReceive('getUrl')->andReturn($url);
        $page->shouldReceive('getContent')->andReturn('foobar');
        $page->shouldReceive('clear')->once();

        /** @var \Psr\Log\LoggerInterface|Mockery\MockInterface $logger */
        $logger = Mockery::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('log')->with('info', 'Saved file pages/foo', [])->once();

        $this->saver->setLogger($logger);
        $this->saver->savePage($page);
    }
}
