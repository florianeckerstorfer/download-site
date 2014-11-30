<?php

namespace FlorianEc\DownloadSite\Tests\Util;

use FlorianEc\DownloadSite\Util\FileUtil;

/**
 * FileUtilTest
 *
 * @package   FlorianEc\DownloadSite\Tests
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2014 Florian Eckerstorfer
 * @group     unit
 */
class FileUtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers FlorianEc\DownloadSite\Util\FileUtil::hasExtension()
     */
    public function hasExtensionReturnsTrueIfFilenameHasExtension()
    {
        $this->assertTrue(FileUtil::hasExtension('file.txt'));
        $this->assertTrue(FileUtil::hasExtension('.htaccess'));
        $this->assertTrue(FileUtil::hasExtension('path/file.txt'));
        $this->assertTrue(FileUtil::hasExtension('file.1'));
    }

    /**
     * @test
     * @covers FlorianEc\DownloadSite\Util\FileUtil::hasExtension()
     */
    public function hasExtensionReturnsFalseIfFilenameHasNoExtension()
    {
        $this->assertFalse(FileUtil::hasExtension('file'));
        $this->assertFalse(FileUtil::hasExtension('file.'));
        $this->assertFalse(FileUtil::hasExtension(''));
        $this->assertFalse(FileUtil::hasExtension('.'));
        $this->assertFalse(FileUtil::hasExtension('/'));
        $this->assertFalse(FileUtil::hasExtension('sub/dir'));
    }
}
