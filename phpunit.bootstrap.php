<?php

declare(strict_types=1);

use Medas\FileSystem\FileSystemPackage;
use Medas\ObjectInstantiator\ObjectInstantiator;
use Medas\ServiceManager\{ServiceConfigBuilder, ServiceManager};

new ServiceManager(function (): ServiceConfigBuilder {
    $config = new ServiceConfigBuilder(ObjectInstantiator::class);

    $config->addPackages([
        FileSystemPackage::instance(),
    ]);

    return $config;
});
