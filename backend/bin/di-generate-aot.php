<?php

require __DIR__.'/../vendor/autoload.php';

$builder = new \eCamp\AoT\FactoryBuilder();
$builder->setVerbose();
$builder->build();
