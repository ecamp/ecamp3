<?php
namespace EcampCore;

use EcampCore\Mvc\HandleDbTransactionListener;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerProviderInterface;
use Zend\ModuleManager\Feature\FormElementProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ValidatorProviderInterface;
use Zend\Mvc\Application;

class Module implements
    ConfigProviderInterface,
    AutoloaderProviderInterface,
    ServiceProviderInterface,
    ControllerProviderInterface,
    FormElementProviderInterface,
    ValidatorProviderInterface,
    BootstrapListenerInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'EcampCore' => __DIR__ . '/src/EcampCore',
                    'EcampCoreTest' => __DIR__ . '/test/EcampCoreTest'
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return include __DIR__ . '/config/service.config.php';
    }

    public function getControllerConfig()
    {
        return include __DIR__ . '/config/controller.config.php';
    }

    public function getFormElementConfig()
    {
        return include __DIR__ . '/config/formelement.config.php';
    }

    public function getValidatorConfig()
    {
        return include __DIR__ . '/config/validator.config.php';
    }

    public function onBootstrap(EventInterface $event)
    {
        date_default_timezone_set("UTC");

        /** @var Application $application */
        $application = $event->getTarget();

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $application->getServiceManager()->get('doctrine.entitymanager.orm_default');

        (new HandleDbTransactionListener($em))->attach($application->getEventManager());

    }

}
