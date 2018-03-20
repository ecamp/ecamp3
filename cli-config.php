<?php

include __DIR__ . '/vendor/autoload.php';

$entityManager = eCampApp::GetEntityManager();
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
