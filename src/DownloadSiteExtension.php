<?php

namespace FlorianEc\DownloadSite;

use Cocur\Pli\Container\ExtensionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * DownloadSiteExtension
 *
 * @package   FlorianEc\DownloadSite
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2014 Florian Eckerstorfer
 *
 * @codeCoverageIgnore
 */
class DownloadSiteExtension implements ExtensionInterface
{
    /**
     * @var string
     */
    private $configDirectory;

    /**
     * @param ContainerBuilder $container
     * @param array $config
     *
     * @return void
     */
    public function buildContainer(ContainerBuilder $container, array $config = [])
    {
        $loader = new YamlFileLoader($container, new FileLocator($this->configDirectory));
        $loader->load('services.yml');
    }

    /**
     * @param string $configDirectory
     *
     * @return void
     */
    public function setConfigDirectory($configDirectory)
    {
        $this->configDirectory = $configDirectory;
    }
}
