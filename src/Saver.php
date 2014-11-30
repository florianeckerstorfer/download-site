<?php

namespace FlorianEc\DownloadSite;

use FlorianEc\DownloadSite\Util\FileUtil;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Saver
 *
 * @package   FlorianEc\DownloadSite
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2014 Florian Eckerstorfer
 */
class Saver
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $targetDirectory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $targetDirectory
     *
     * @return Saver
     */
    public function setTargetDirectory($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;

        return $this;
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return Saver
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param Page $page
     *
     * @return void
     */
    public function savePage(Page $page)
    {
        $path = urldecode($page->getUrl()->getPath());
        $filename = $this->targetDirectory.DIRECTORY_SEPARATOR.ltrim($path);
        if (FileUtil::hasExtension($filename) === false) {
            $filename = sprintf('%s%sindex.html', rtrim($filename, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);
        }

        $dirname = dirname($filename);
        if (!file_exists($dirname)) {
            $this->filesystem->mkdir($dirname);
        }

        file_put_contents($filename, $page->getContent());
        $this->log('info', sprintf('Saved file %s', $path));

        // Free some memory
        $page->clear();
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
