<?php

include __DIR__ . '/autoload.php';

$entityManager = eCampApp::GetEntityManager();
return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
