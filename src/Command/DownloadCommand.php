<?php

namespace FlorianEc\DownloadSite\Command;

use FlorianEc\DownloadSite\Downloader;
use FlorianEc\DownloadSite\Page;
use FlorianEc\DownloadSite\Saver;
use FlorianEc\DownloadSite\Site;
use League\Url\UrlImmutable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * DownloadCommand
 *
 * @package   FlorianEc\DownloadSite\Command
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2014 Florian Eckerstorfer
 */
class DownloadCommand extends Command
{
    /**
     * @var Downloader
     */
    private $downloader;

    /**
     * @var Saver
     */
    private $saver;

    /**
     * @param Downloader $downloader
     */
    public function __construct(Downloader $downloader, Saver $saver)
    {
        $this->downloader = $downloader;
        $this->saver      = $saver;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('download')
             ->setDescription('Downloads the given site.')
             ->addArgument('url', InputArgument::REQUIRED, 'URL to download')
             ->addArgument('target-directory', InputArgument::REQUIRED, 'Directory to save site to');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = new ConsoleLogger($output);
        $this->downloader->setLogger($logger);
        $this->saver->setLogger($logger);

        $site = new Site(UrlImmutable::createFromUrl($input->getArgument('url')));
        $this->saver->setTargetDirectory($input->getArgument('target-directory'));

        $this->download($site, $output);
    }

    /**
     * @param Site $site
     * @param OutputInterface $output
     *
     * @return int Number of downloaded pages
     */
    protected function download(Site $site, OutputInterface $output)
    {
        $output->writeln(sprintf('Download site <info>%s</info>', $site->getRootUrl()));

        $this->downloader->download($site);

        $output->writeln('');
        $output->writeln('PAGES:');
        foreach ($site->getPages() as $page) {
            $output->writeln(sprintf('- <comment>%s</comment>', $page->getUrl()));
        }
        $output->writeln(sprintf('Downloaded <info>%d pages</info>.', $site->getPageCount()));

        return $site->getPageCount();
    }
}
