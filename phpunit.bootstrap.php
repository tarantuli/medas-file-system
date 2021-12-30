<?php

declare(strict_types=1);

use Medas\ConfigManager\ConfigManager;
use Medas\FileSystem\FileSystemPackage;
use Medas\ServiceManager\ServiceManager;

$sm = ServiceManager::get();
$sm->addPackage(new FileSystemPackage());

/** @var $config ConfigManager */
$config = $sm->resolve(ConfigManager::class);
$config->readEnv(__DIR__);
