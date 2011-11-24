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


Zend_Loader_Autoloader::autoload("Core\Entity\Annotations\Property");
Zend_Loader_Autoloader::autoload("Core\Entity\Annotations\PropertyEntity");
Zend_Loader_Autoloader::autoload("Core\Entity\Annotations\PropertyEntityList");
Zend_Loader_Autoloader::autoload("Core\Entity\Annotations\Method");
Zend_Loader_Autoloader::autoload("Core\Entity\Annotations\MethodEntity");
Zend_Loader_Autoloader::autoload("Core\Entity\Annotations\MethodEntityList");

\Core\Entity\Wrapper\Factory::createFiles('\Core\Entity\Camp');
\Core\Entity\Wrapper\Factory::createFiles('\Core\Entity\Day');
\Core\Entity\Wrapper\Factory::createFiles('\Core\Entity\Event');
\Core\Entity\Wrapper\Factory::createFiles('\Core\Entity\EventInstance');
\Core\Entity\Wrapper\Factory::createFiles('\Core\Entity\Group');
\Core\Entity\Wrapper\Factory::createFiles('\Core\Entity\Login');
\Core\Entity\Wrapper\Factory::createFiles('\Core\Entity\Period');
\Core\Entity\Wrapper\Factory::createFiles('\Core\Entity\Plugin');
\Core\Entity\Wrapper\Factory::createFiles('\Core\Entity\Subcamp');
\Core\Entity\Wrapper\Factory::createFiles('\Core\Entity\User');
\Core\Entity\Wrapper\Factory::createFiles('\Core\Entity\UserCamp');
\Core\Entity\Wrapper\Factory::createFiles('\Core\Entity\UserGroup');
\Core\Entity\Wrapper\Factory::createFiles('\Core\Entity\UserRelationship');


echo "Done";


