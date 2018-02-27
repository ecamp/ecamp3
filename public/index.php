<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

include_once dirname(__DIR__) . '/vendor/autoload.php';

eCampApp::CreateApp()->run();
