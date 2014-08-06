<?php

define("__BASE__", dirname(__DIR__));
define("__PUBLIC__", __BASE__ . '/public');
define("__VENDOR__", __BASE__ . '/vendor');
define("__DATA__", __BASE__ . '/data');

define("__ENV_DEV__", 'dev');
define("__ENV_TEST__", 'test');
define("__ENV_PROD__", 'prod');

if (!defined("__ENV__")) {
    $envFile = __DIR__ . '/env.php';
    define("__ENV__", file_exists($envFile) ? include $envFile : __ENV_PROD__);
}
