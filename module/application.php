<?php

chdir(__DIR__ . '/..');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

$appConfigFile = __DIR__ . '/../config/application.config.php';
$devConfigFile = __DIR__ . '/../config/development.config.php';

/** @noinspection PhpIncludeInspection */
$appConfig = include $appConfigFile;

if (file_exists($devConfigFile)) {
    /** @noinspection PhpIncludeInspection */
    $devConfig = include $devConfigFile;
    $appConfig = \Zend\Stdlib\ArrayUtils::merge($appConfig, $devConfig);
}

// Initialize the application!
return \Zend\Mvc\Application::init($appConfig);
