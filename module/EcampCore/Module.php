<?php
namespace EcampCore;

use EcampCore\DB\DatabaseFlushListener;
use EcampCore\DB\DatabaseTransactionListener;
use EcampCore\I18n\Translator\TranslatorEventListener;
use EcampLib\ModuleManager\Feature\JobFactoryProviderInterface;
use EcampLib\ModuleManager\Feature\PrintableProviderInterface;
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
    JobFactoryProviderInterface,
    PrintableProviderInterface,
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

    public function getJobFactoryConfig()
    {
        return include __DIR__ . '/config/job.config.php';
    }

    public function getPrintableConfig()
    {
        return include __DIR__ . '/config/printable.config.php';
    }

    public function onBootstrap(EventInterface $event)
    {
        date_default_timezone_set("UTC");

        /** @var Application $application */
        $application = $event->getTarget();

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $application->getServiceManager()->get('doctrine.entitymanager.orm_default');

        (new DatabaseTransactionListener($em))->attach($application->getEventManager());
        (new DatabaseFlushListener($em))->attachShared($application->getEventManager()->getSharedManager());

        /** @var \Zend\Mvc\I18n\Translator $mvcTranslator */
        $mvcTranslator = $application->getServiceManager()->get('MvcTranslator');
        /** @var \Zend\I18n\Translator\Translator $translator */
        $translator = $mvcTranslator->getTranslator();

        $translator->setLocale('en');

        (new TranslatorEventListener($em))->attach($translator->getEventManager());
    }

}
