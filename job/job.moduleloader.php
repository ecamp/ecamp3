<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */

chdir(dirname(__DIR__));

ini_set('display_errors', true);
error_reporting((E_ALL | E_STRICT) ^ E_NOTICE);

// Setup autoloading
require 'init_autoloader.php';

$configuration = require 'config/job.config.php';
$moduleManager = new Zend\ModuleManager\ModuleManager($configuration['modules']);
$listenerOptions  = new Zend\ModuleManager\Listener\ListenerOptions($configuration['module_listener_options']);

$moduleAutoloader = new Zend\Loader\ModuleAutoloader($listenerOptions->getModulePaths());
$moduleAutoloader->register();

$events = $moduleManager->getEventManager();
$events->attach(Zend\ModuleManager\ModuleEvent::EVENT_LOAD_MODULE_RESOLVE, new Zend\ModuleManager\Listener\ModuleResolverListener);
$events->attach(Zend\ModuleManager\ModuleEvent::EVENT_LOAD_MODULE, new Zend\ModuleManager\Listener\AutoloaderListener);
$moduleManager->loadModules();
