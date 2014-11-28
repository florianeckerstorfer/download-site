<?php

namespace FlorianEc\DownloadSite;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var callback
     */
    private $savePageCallback = null;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
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
     * @param callback $callback
     *
     * @return Saver
     */
    public function setSavePageCallback($callback)
    {
        if (is_callable($callback) === false) {
            throw new InvalidArgumentException('The given callback is not callable.');
        }

        $this->savePageCallback = $callback;

        return $this;
    }

    /**
     * @param Site   $site
     * @param string $targetDirectory
     *
     * @return integer
     */
    public function save(Site $site, $targetDirectory)
    {
        $count = 0;
        foreach ($site->getPages() as $page) {
            $this->savePage($page, $targetDirectory);
            $count++;
        }

        return $count;
    }

    /**
     * @param Page   $page
     * @param string $targetDirectory
     *
     * @return void
     */
    protected function savePage(Page $page, $targetDirectory)
    {
        $path = urldecode($page->getUrl()->getPath());
        $filename = Path::makeAbsolute(ltrim($path, '/'), $targetDirectory);
        if (FileUtil::hasExtension($filename) === false) {
            $filename = sprintf('%s%sindex.html', rtrim($filename, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);
        }

        $dirname = dirname($filename);
        if (!file_exists($dirname)) {
            $this->filesystem->mkdir($dirname);
        }

        file_put_contents($filename, $page->getContent());
        $this->log('debug', sprintf('Saved file %s', $page->getUrl()->getPath()));
        if ($this->savePageCallback) {
            call_user_func($this->savePageCallback, $page);
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
