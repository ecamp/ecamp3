<?php

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

require_once __DIR__ . '/vendor/autoload.php';
$em = eCampApp::GetEntityManager();

$loader = new Loader();
$loader->loadFromDirectory(__DIR__ . '/module/eCampCore/data');

$executor = new ORMExecutor($em, new ORMPurger());
$executor->execute($loader->getFixtures(), true);
