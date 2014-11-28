<?php

require_once __DIR__.'/../vendor/autoload.php';

use Cocur\Pli\Pli;
use FlorianEc\DownloadSite\DownloadSiteConfiguration;
use FlorianEc\DownloadSite\DownloadSiteExtension;

$pli = new Pli(__DIR__.'/../config');
$config = $pli->loadConfiguration(new DownloadSiteConfiguration(), ['config.yml']);
$container = $pli->buildContainer(new DownloadSiteExtension(), $config);
$application = $pli->getApplication($container);
$application->run();
