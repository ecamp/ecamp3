<?php

use Laminas\Mvc\Application;
use Laminas\Mvc\Service\ServiceManagerConfig;
use Laminas\ServiceManager\ServiceManager;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class eCampApp {
    /** @var Application */
    private static $instance;

    public static function CreateApp(): Application {
        $config = self::GetAppConfig();

        return Application::init($config);
    }

    public static function CreateServiceManagerWithoutDi(): ServiceManager {
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

    public static function App(): Application {
        if (null == self::$instance) {
            self::$instance = self::CreateApp();
        }

        return self::$instance;
    }

    public static function Reset(): void {
        self::$instance = null;
    }

    public static function Run(): void {
        self::App()->run();
    }

    public static function ServiceManager() {
        return self::App()->getServiceManager();
    }

    public static function GetService($name) {
        return self::ServiceManager()->get($name);
    }

    public static function GetEntityManager(string $name = 'orm_default'): Doctrine\ORM\EntityManager {
        return self::GetService('doctrine.entitymanager.'.$name);
    }

    public static function CreateSetup(): Application {
        $config = self::GetSetupConfig();

        return Application::init($config);
    }

    public static function RegisterErrorHandler(): void {
        // if sentry-configuration available, use sentry
        $sentryConfig = __DIR__.'/../config/sentry.config.php';

        if (file_exists($sentryConfig)) {
            Sentry\init(include $sentryConfig);
        } else {
            eCampApp::RegisterWhoops();
        }
    }

    public static function RegisterWhoops($handler = PrettyPageHandler::class): void {
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
