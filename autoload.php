<?php

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    $isCli = (PHP_SAPI === 'cli');

    echo $isCli ? PHP_EOL : "<br />";
    echo "  Autoloader is missing.";
    echo $isCli ? PHP_EOL : "<br />";
    echo "  Run composer install!";
    echo $isCli ? PHP_EOL : "<br />";

    if ($isCli) {
        echo "  https://getcomposer.org/";
    } else {
        echo "  <a href='https://getcomposer.org/'>https://getcomposer.org/</a>";
    }

    echo $isCli ? PHP_EOL : "<br />";
    echo $isCli ? PHP_EOL : "<br />";
    die();
}

include_once __DIR__ . '/vendor/autoload.php';
