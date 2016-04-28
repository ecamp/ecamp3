<?php

if( file_exists('define.local.php'))
    include 'define.local.php';

define("__BASE__", dirname(__DIR__));
define("__DATA__", __BASE__ . '/data');
define("__VENDOR__", __BASE__ . '/vendor');
define("__MODULE__", __BASE__ . '/module');
define("__PLUGINS__", __BASE__ . '/plugins');
define("__PUBLIC__", __BASE__ . '/public');
define("__ASSETS__", __BASE__ . '/public/assets/vendor');

defined("__BASE_URL__") || define("__BASE_URL__" , 'http://ecamp3.dev');
