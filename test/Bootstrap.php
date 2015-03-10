<?php

use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

error_reporting(E_ALL | E_STRICT);
chdir(dirname(__DIR__));

/**
 * Test bootstrap, for setting up autoloading
 */
class Bootstrap
{
    protected static $serviceManager;

    public static function init()
    {
        require './init_autoloader.php';
        require_once './config/define.php';


        // use ModuleManager to load this module and it's dependencies
        $config = array(
            'module_listener_options' => array(
                'module_paths' => array(
                    './module',
                    './vendor',
                    './plugins'
                ),
                'config_glob_paths' => array(
                    'config/app.autoload/{,*.}{global,local}.php',
                    'config/common.autoload/{,*.}{global,local}.php',
                )
            ),
            'modules' => array(
                'EcampLib',
                'EcampCore',
                'DoctrineModule',
                'DoctrineORMModule',
            )
        );

        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();
        static::$serviceManager = $serviceManager;
    }

    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

}

Bootstrap::init();
