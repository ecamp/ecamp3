<?php
namespace EcampApi;

use Zend\Mvc\MvcEvent;

use EcampApi\Listener\CollectionRenderingListener;
use EcampApi\Resource\Camp\CampResourceListener;
use EcampApi\Resource\User\UserResourceListener;
use EcampApi\Resource\Collaboration\CollaborationResourceListener;
use EcampApi\Resource\Period\PeriodResourceListener;
use EcampApi\Resource\Day\DayResourceListener;
use EcampApi\Resource\Event\EventResourceListener;
use EcampApi\Resource\EventInstance\EventInstanceResourceListener;
use EcampApi\Resource\EventResp\EventRespResourceListener;
use EcampApi\Resource\EventCategory\EventCategoryResourceListener;
use EcampApi\Resource\Group\GroupResourceListener;
use EcampApi\Resource\Membership\MembershipResourceListener;
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
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
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
