<?php
namespace EcampLib;

use EcampLib\Job\JobFlushListener;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\ModuleManager\Feature\FormElementProviderInterface;
use Zend\ModuleManager\Feature\FilterProviderInterface;
use Zend\ModuleManager\Feature\ValidatorProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Mvc\MvcEvent;

class Module implements
    ServiceProviderInterface,
    ViewHelperProviderInterface,
    FormElementProviderInterface,
    FilterProviderInterface,
    ValidatorProviderInterface,
    InitProviderInterface
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

    public function init(ModuleManagerInterface $moduleManager)
    {
        /** @var \Zend\ModuleManager\ModuleManager $moduleManager */
        $sm = $moduleManager->getEvent()->getParam('ServiceManager');
        /** @var \Zend\ModuleManager\Listener\ServiceListener $serviceListener */
        $serviceListener = $sm->get('ServiceListener');
        $serviceListener->addServiceManager(
            'JobFactoryManager',
            'job_factory_manager',
            'EcampLib\ModuleManager\Feature\JobFactoryProviderInterface',
            'getJobFactoryConfig'
        );
        $serviceListener->addServiceManager(
            'PrintableManager',
            'printable_manager',
            'EcampLib\ModuleManager\Feature\PrintableProviderInterface',
            'getPrintableConfig'
        );
    }

    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();

        /** @var \EcampLib\Job\JobQueue $jobQueue */
        $jobQueue = $application->getServiceManager()->get('EcampLib\Job\JobQueue');

        (new JobFlushListener($jobQueue))->attach($application->getEventManager());
    }
}

require_once __DIR__ . '/src/EcampLib/Util/password.php';
