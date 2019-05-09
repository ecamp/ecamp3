<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

while(!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "Waiting for composer install to finish...\n";
    sleep(5);
}
