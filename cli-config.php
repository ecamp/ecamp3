<?php

require_once __DIR__ . '/module/eCampApp.php';

$entityManager = eCampApp::GetService('doctrine.entitymanager.orm_default');
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
