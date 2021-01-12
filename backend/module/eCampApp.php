<?php

use Laminas\Mvc\Application;
use Laminas\Mvc\Service\ServiceManagerConfig;
use Laminas\ServiceManager\ServiceManager;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class eCampApp {
    /** @var Application */
    private static $instance;

    /** @return Application */
    public static function CreateApp() {
        $config = self::GetAppConfig();

        return Application::init($config);
    }

    /** @return ServiceManager */
    public static function CreateServiceManagerWithoutDi() {
        // remove 'Laminas\Di' config
        $configuration = self::GetAppConfig();
        unset($configuration['modules'][array_search('Laminas\Di', $configuration['modules'])]);

        // Prepare the service manager
        $smConfig = isset($configuration['service_manager']) ? $configuration['service_manager'] : [];
        $smConfig = new ServiceManagerConfig($smConfig);

        $serviceManager = new ServiceManager();
        $smConfig->configureServiceManager($serviceManager);
        $serviceManager->setService('ApplicationConfig', $configuration);

        // Remove 'eCamp\AoT' module and load rest of the modules
        $mm = $serviceManager->get('ModuleManager');
        $modules = $mm->getModules();
        unset($modules[array_search('eCamp\AoT', $modules)]);
        $mm->setModules($modules);
        $mm->loadModules();

        return $serviceManager;
    }

    /** @return Application */
    public static function App() {
        if (null == self::$instance) {
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
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public static function GetEntityManager($name = 'orm_default') {
        return self::GetService('doctrine.entitymanager.'.$name);
    }

    /** @return Application */
    public static function CreateSetup() {
        $config = self::GetSetupConfig();

        return Application::init($config);
    }

    public static function RegisterErrorHandler() {
        // if sentry-configuration available, use sentry
        $sentryConfig = __DIR__.'/../config/sentry.config.php';

        if (file_exists($sentryConfig)) {
            Sentry\init(include $sentryConfig);
        } else {
            eCampApp::RegisterWhoops();
        }
    }

    public static function RegisterWhoops($handler = PrettyPageHandler::class) {
        $whoops = new Run();
        $whoops->pushHandler(new $handler());
        $whoops->register();
    }

    private static function GetAppConfig() {
        $appConfigFile = __DIR__.'/../config/application.config.php';
        $devConfigFile = __DIR__.'/../config/development.config.php';

        /** @noinspection PhpIncludeInspection */
        $appConfig = include $appConfigFile;

        if (file_exists($devConfigFile)) {
            /** @noinspection PhpIncludeInspection */
            $devConfig = include $devConfigFile;
            $appConfig = \Laminas\Stdlib\ArrayUtils::merge($appConfig, $devConfig);
        }

        return $appConfig;
    }

    private static function GetSetupConfig() {
        $setupConfigFile = __DIR__.'/../config/setup.config.php';

        // @noinspection PhpIncludeInspection
        return include $setupConfigFile;
    }
}
