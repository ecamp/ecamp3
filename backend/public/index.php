<?php

// production
// error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_USER_DEPRECATED);

// development
error_reporting(E_ALL);

ini_set('display_errors', 1);

require_once __DIR__.'/../autoload.php';
chdir(dirname(__DIR__));

eCampApp::Run();
