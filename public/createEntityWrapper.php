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

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();



$e = new ReflectionClass('\Core\Entity\Login');
$ar = new Doctrine\Common\Annotations\AnnotationReader();
$ar->setDefaultAnnotationNamespace("CoreApi");

Zend_Loader_Autoloader::autoload('\CoreApi\Wrapper');
$w = new \CoreApi\Wrapper(array());



foreach($e->getProperties() as $p)
{
	//var_dump($p);
	//var_dump($p->getDocComment());
	
	$pa = $ar->getPropertyAnnotations($p);
	
	var_dump($pa);
	
}