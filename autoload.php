<?php

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    $isCli = (PHP_SAPI === 'cli');

    if ($isCli) {
        echo PHP_EOL;
        echo "  Installation is not complete.";
        echo PHP_EOL;
        echo "  Visit setup.php";
        echo PHP_EOL;
        echo PHP_EOL;
    } else {
        header("location: setup.php");
        echo "Installation is not complete.";
        echo "<br />";
        echo "Visit setup.php";
    }

    die();
}

include_once __DIR__ . '/vendor/autoload.php';
