<?php

declare(strict_types=1);

use Medas\FileSystem\FileSystemPackage;
use Medas\ServiceManager\ServiceManager;

$sm = ServiceManager::get();
$sm->addPackage(FileSystemPackage::instance());
