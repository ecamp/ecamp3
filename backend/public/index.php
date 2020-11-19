<?php

if ('prod' === getenv('env')) {
    // production
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__.'/../data/error_log');
} else {
    // development
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

require_once __DIR__.'/../autoload.php';
chdir(dirname(__DIR__));

eCampApp::RegisterErrorHandler();
eCampApp::Run();
