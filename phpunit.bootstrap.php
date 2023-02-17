<?php

declare(strict_types=1);

use Medas\FileSystem\FileSystemPackage;
use Medas\ServiceManager\{ServiceConfig, ServiceManager};

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig();

    $config->addPackages([
        FileSystemPackage::instance(),
    ]);

    return $config;
});
