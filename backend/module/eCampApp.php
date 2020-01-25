<?php

use Zend\Mvc\Application;

class eCampApp {
    private static $instance;

    private static function GetAppConfig() {
        $appConfigFile = __DIR__ . '/../config/application.config.php';
        $devConfigFile = __DIR__ . '/../config/development.config.php';

        /** @noinspection PhpIncludeInspection */
        $appConfig = include $appConfigFile;

        if (file_exists($devConfigFile)) {
            /** @noinspection PhpIncludeInspection */
            $devConfig = include $devConfigFile;
            $appConfig = \Zend\Stdlib\ArrayUtils::merge($appConfig, $devConfig);
        }

        return $appConfig;
    }

    /** @return Application */
    public static function CreateApp() {
        $config = self::GetAppConfig();
        return Application::init($config);
    }

     /** @return Application */
     public static function CreateAppWithoutDi() {
        $config = self::GetAppConfig();
        unset( $config['modules'][ array_search('Zend\Di', $config['modules']) ] );
        return Application::init($config);
    }

    /** @return Application */
    public static function App() {
        if (self::$instance == null) {
            self::$instance = self::CreateApp();
        }
        return self::$instance;
    }

    public static function Reset() {
        self::$instance = null;
    }

    public static function Run() {
        self::App()->run();
    }

    public static function ServiceManager() {
        return self::App()->getServiceManager();
    }

    public static function GetService($name) {
        return self::ServiceManager()->get($name);
    }

    /**
     * @param string $name
     * @return \Doctrine\ORM\EntityManager
     */
    public static function GetEntityManager($name = 'orm_default') {
        return self::GetService('doctrine.entitymanager.' . $name);
    }



    private static function GetSetupConfig() {
        $setupConfigFile = __DIR__ . '/../config/setup.config.php';

        /** @noinspection PhpIncludeInspection */
        return include $setupConfigFile;
    }

    /** @return Application */
    public static function CreateSetup() {
        $config = self::GetSetupConfig();
        return Application::init($config);
    }
}
