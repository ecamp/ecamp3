<?php

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

require_once __DIR__ . '/module/eCampApp.php';
$em = eCampApp::GetService('doctrine.entitymanager.orm_default');

$loader = new Loader();
$loader->loadFromDirectory(__DIR__ . '/module/eCampCore/data');

$executor = new ORMExecutor($em, new ORMPurger());
$executor->execute($loader->getFixtures(), true);
