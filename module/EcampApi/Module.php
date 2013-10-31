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

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'EcampApi\Resource\Camp\CampResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\Camp');

                    return new CampResourceListener($repository);
                },

                'EcampApi\Resource\User\UserResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\User');

                    return new UserResourceListener($repository);
                },

                'EcampApi\Resource\Collaboration\CollaborationResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\CampCollaboration');

                    return new CollaborationResourceListener($repository);
                },

                'EcampApi\Resource\Period\PeriodResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\Period');

                    return new PeriodResourceListener($repository);
                },

                'EcampApi\Resource\Day\DayResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\Day');

                    return new DayResourceListener($repository);
                },

                'EcampApi\Resource\Event\EventResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\Event');

                    return new EventResourceListener($repository);
                },

                'EcampApi\Resource\EventInstance\EventInstanceResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\EventInstance');

                    return new EventInstanceResourceListener($repository);
                },

                'EcampApi\Resource\EventResp\EventRespResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\EventResp');

                    return new EventRespResourceListener($repository);
                },

                'EcampApi\Resource\EventCategory\EventCategoryResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\EventCategory');

                    return new EventCategoryResourceListener($repository);
                },

                'EcampApi\Resource\Group\GroupResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\Group');

                    return new GroupResourceListener($repository);
                },

                'EcampApi\Resource\Membership\MembershipResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\GroupMembership');

                    return new MembershipResourceListener($repository);
                },

                'EcampApi\Resource\Search\UserResourceListener' => function ($services) {
                    $userRepo = $services->get('EcampCore\Repository\User');
                    $groupRepo = $services->get('EcampCore\Repository\Group');
                    $campRepo = $services->get('EcampCore\Repository\Camp');

                    return new \EcampApi\Resource\Search\UserResourceListener($userRepo, $groupRepo, $campRepo);
                },

            ),

        );
    }
}
