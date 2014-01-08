<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */

define('__PUBLIC__' , __DIR__);
define('__VENDOR__', __DIR__ . '/../vendor');
define('__COMPONENTS__', __DIR__ . '/../components');

chdir(dirname(__DIR__));

ini_set('display_errors', true);
error_reporting((E_ALL | E_STRICT) ^ E_NOTICE);

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
