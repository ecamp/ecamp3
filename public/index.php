<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));


// if(! file_exists(APPLICATION_PATH . "/../local.conf.php"))
// {
// 	copy(APPLICATION_PATH . "/../default.conf.php", APPLICATION_PATH . "/../local.conf.php");
// 	throw new Exception("Set correct values to the file local.conf.php!!!");
// }
// require_once APPLICATION_PATH . "/../local.conf.php";



$autoloadClasses = array();
spl_autoload_register(
	function($classname) use (&$autoloadClasses)
	{	array_push($autoloadClasses, $classname);	return false;	});


/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

//die(print_r($application));

$application->run();




die();

echo "<hr/><pre>";
print_r($autoloadClasses);
echo "</pre>";