<?php
namespace EcampApi;

use Zend\Mvc\MvcEvent;

use EcampApi\Listener\CollectionRenderingListener;
use Zend\Authentication\AuthenticationService;

class Module
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
                    'EcampApi' => __DIR__ . '/src/EcampApi',
                    'EcampApiTest' => __DIR__ . '/test/EcampApiTest'
                ),
            ),
        );
    }

    public function getControllerConfig()
    {
        return include __DIR__ . '/config/controller.config.php';
    }

    public function onBootstrap(MvcEvent $event)
    {
        $application = $event->getTarget();
        $serviceManager = $application->getServiceManager();
        $config = $serviceManager->get('Config');

        $sharedEventManager = $event->getTarget()->getEventManager()->getSharedManager();

        (new CollectionRenderingListener())->attachShared($sharedEventManager);

        $sharedEventManager->attach('PhlyRestfully\ResourceController', MvcEvent::EVENT_DISPATCH, function(MvcEvent $e){
            $authService = new AuthenticationService();
            if (!$authService->hasIdentity()) {
                $url = $e->getRouter()->assemble(array(), array('name' => 'api/login'));

                $response = $e->getResponse();
                /* @var $response \Zend\Http\PhpEnvironment\Response */
                $response->getHeaders()->addHeaderLine('Location', $url);
                $response->setStatusCode(302);
                $response->sendHeaders();
                exit;
            }
        });
    }

}
