<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */

chdir(__DIR__);

ini_set('display_errors', true);
error_reporting((E_ALL | E_STRICT) ^ E_NOTICE);

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
$app = Zend\Mvc\Application::init(require 'config/job.config.php');

\EcampLib\Job\AbstractBootstrappedJobBase::setServiceLocator($app->getServiceManager());

echo "STRAPPED";
