<?php

if ('cli-server' === php_sapi_name()) {
    $path = realpath(__DIR__.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

include 'index.php';
