<?php

include __DIR__ . '/vendor/autoload.php';

$entityManager = eCampApp::GetService('doctrine.entitymanager.orm_default');
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
