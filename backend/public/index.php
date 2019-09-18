<?php

error_reporting(E_ERROR);
ini_set("display_errors", 1);

require_once __DIR__ . '/../autoload.php';
chdir(dirname(__DIR__));

eCampApp::Run();
