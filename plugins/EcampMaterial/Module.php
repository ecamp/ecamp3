<?php
namespace EcampMaterial;

use EcampMaterial\Listener\CollectionRenderingListener;

use Zend\Mvc\MvcEvent;

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
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return include __DIR__ . '/config/service.config.php';
    }

    public function onBootstrap(MvcEvent $event)
    {
        $application = $event->getTarget();
        $serviceManager = $application->getServiceManager();
        $config = $serviceManager->get('Config');

        $sharedEventManager = $event->getTarget()->getEventManager()->getSharedManager();

        (new CollectionRenderingListener())->attachShared($sharedEventManager);

//     	$sharedEventManager->attach('PhlyRestfully\ResourceController', MvcEvent::EVENT_DISPATCH, function(MvcEvent $e){
//     		$authService = new AuthenticationService();
//     		if (!$authService->hasIdentity()) {
//     			$url = $e->getRouter()->assemble(array(), array('name' => 'api/login'));

//     			$response = $e->getResponse();
//     			/* @var $response \Zend\Http\PhpEnvironment\Response */
//     			$response->getHeaders()->addHeaderLine('Location', $url);
//     			$response->setStatusCode(302);
//     			$response->sendHeaders();
//     			exit;
//     		}
//     	});
    }

}
