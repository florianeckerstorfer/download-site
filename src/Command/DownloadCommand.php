<?php

namespace FlorianEc\DownloadSite\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
     * @return void
     */
    protected function configure()
    {
        $this->setName('download')
             ->setDescription('Downloads the given site.')
             ->addArgument('url', InputArgument::REQUIRED, 'URL to download');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Download site <info>%s</info>', $input->getArgument('url')));
    }
}
