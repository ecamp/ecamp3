<?php

class eCampApp
{
    private static $instance;

    private static function GetConfig() {
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

    /** @return \Zend\Mvc\Application */
    public static function CreateApp() {
        $config = self::GetConfig();
        return \Zend\Mvc\Application::init($config);
    }

    /** @return \Zend\Mvc\Application */
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

    public static function GetEntityManager($name = 'orm_default') {
        return self::GetService('doctrine.entitymanager.' . $name);
    }
}
