<?php

class eCampApp
{
    private static $instance;

    /**
     * @return \Zend\Mvc\Application
     */
    public static function App() {
        if (self::$instance == null) {
            self::$instance = include __DIR__ . '/application.php';
        }
        return self::$instance;
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
}
