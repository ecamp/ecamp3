<?php
namespace EcampLib;

use EcampLib\Entity\ServiceLocatorAwareEventListener;
use EcampLib\Listener\FlushEntitiesListener;

use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\Feature\FormElementProviderInterface;
use Zend\ModuleManager\Feature\FilterProviderInterface;
use Zend\ModuleManager\Feature\ValidatorProviderInterface;
use Zend\Mvc\MvcEvent;

class Module implements
    ServiceProviderInterface,
    ViewHelperProviderInterface,
    FormElementProviderInterface,
    FilterProviderInterface,
    ValidatorProviderInterface
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
                    'EcampLib' => __DIR__ . '/src/EcampLib',
                    'EcampLibTest' => __DIR__ . '/test/EcampLibTest'
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return include __DIR__ . '/config/service.config.php';
    }

    public function getViewHelperConfig()
    {
        return include __DIR__ . '/config/view.helper.config.php';
    }

    public function getFormElementConfig()
    {
        return include __DIR__ . '/config/formelement.config.php';
    }

    public function getFilterConfig()
    {
        return include __DIR__ . '/config/filter.config.php';
    }

    public function getValidatorConfig()
    {
        return include __DIR__ . '/config/validator.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $sm = $e->getApplication()->getServiceManager();

        $em = $sm->get('doctrine.entitymanager.orm_default');
        $em->getEventManager()->addEventSubscriber(new ServiceLocatorAwareEventListener($sm));

        /* listener for flushing entity manager */
        $eventManager = $e->getTarget()->getEventManager();
        $eventManager->attach(new FlushEntitiesListener());

        \Resque::setBackend('localhost:6379', 0, 'resque.ecamp3');
    }
}

require_once __DIR__ . '/src/' . __NAMESPACE__ . '/Util/password.php';
