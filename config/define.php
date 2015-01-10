<?php

if(is_file(__DIR__ . '/define.local.php')){
    include __DIR__ . '/define.local.php';
}

defined('__DOMAIN__')   || define("__DOMAIN__", 'http://localhost');
defined('__BASE__')     || define("__BASE__", dirname(__DIR__));
defined('__DATA__')     || define("__DATA__", __BASE__ . '/data');
defined('__VENDOR__')   || define("__VENDOR__", __BASE__ . '/vendor');
defined('__MODULE__')   || define("__MODULE__", __BASE__ . '/module');
defined('__PLUGINS__')  || define("__PLUGINS__", __BASE__ . '/plugins');
defined('__PUBLIC__')   || define("__PUBLIC__", __BASE__ . '/public');
