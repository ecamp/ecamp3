<?php
namespace EcampApi;

use Zend\Mvc\MvcEvent;

use EcampApi\Listener\JsonExceptionStrategy;
use EcampApi\Listener\AuthenticationRequiredExceptionStrategy;
use EcampApi\Resource\Camp\CampResourceListener;
use EcampApi\Resource\User\UserResourceListener;
use EcampApi\Resource\Collaboration\CollaborationResourceListener;
use EcampApi\Resource\Period\PeriodResourceListener;
use EcampApi\Resource\Day\DayResourceListener;
use EcampApi\Resource\Event\EventResourceListener;
use EcampApi\Resource\EventInstance\EventInstanceResourceListener;
use EcampApi\Resource\EventResp\EventRespResourceListener;
use EcampApi\Resource\EventCategory\EventCategoryResourceListener;
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

        // Config json enabled exceptionStrategy
        $exceptionStrategy = new JsonExceptionStrategy();

        $displayExceptions = false;

        if (isset($config['view_manager']['display_exceptions'])) {
            $displayExceptions = $config['view_manager']['display_exceptions'];
        }

        $exceptionStrategy->setDisplayExceptions($displayExceptions);
        $exceptionStrategy->attach($application->getEventManager());

        $authenticationRequiredStrategy = new AuthenticationRequiredExceptionStrategy();
        $authenticationRequiredStrategy->attach($application->getEventManager());

        $sharedEventManager = $event->getTarget()->getEventManager()->getSharedManager();
        /*
         * collection rendering
         * specify here which classes to use for resource rendering in collections
         */
        $sharedEventManager->attach('PhlyRestfully\Plugin\HalLinks', 'renderCollection.resource', function ($e) {
            $resource = $e->getParam('resource');
            $params = $e->getParams();

            if ($resource instanceof \EcampCore\Entity\Camp) {
                $params['resource']    = new Resource\Camp\CampBriefResource($resource);

                return;
            }

            if ($resource instanceof \EcampCore\Entity\User) {
                $params['resource']    = new Resource\User\UserBriefResource($resource);

                return;
            }

            if ($resource instanceof \EcampCore\Entity\CampCollaboration) {
                $params['resource']    = new Resource\Collaboration\CollaborationResource($resource);

                return;
            }

            if ($resource instanceof \EcampCore\Entity\Period) {
                $params['resource']    = new Resource\Period\PeriodBriefResource($resource);

                return;
            }

            if ($resource instanceof \EcampCore\Entity\Day) {
                $params['resource']    = new Resource\Day\DayBriefResource($resource);

                return;
            }

            if ($resource instanceof \EcampCore\Entity\Event) {
                $params['resource']    = new Resource\Event\EventBriefResource($resource);

                return;
            }

            if ($resource instanceof \EcampCore\Entity\EventInstance) {
                $params['resource']    = new Resource\EventInstance\EventInstanceBriefResource($resource);

                return;
            }

            if ($resource instanceof \EcampCore\Entity\EventResp) {
                $params['resource']    = new Resource\EventResp\EventRespBriefResource($resource);

                return;
            }

            if ($resource instanceof \EcampCore\Entity\EventCategory) {
                $params['resource']    = new Resource\EventCategory\EventCategoryBriefResource($resource);

                return;
            }
        }, 100);

        /*
         * additional paginator attrbiutes for collections
         */
        $sharedEventManager->attach(
            'PhlyRestfully\Plugin\HalLinks',
            array('renderResource', 'renderCollection'),
            function ($e) {

                $collection = $e->getParam('collection');
                $paginator = $collection->collection;

                if (!$paginator instanceof \Zend\Paginator\Paginator) {
                    return;
                }

                /* page number and size is not yet set by phplyrestfully */
                $paginator->setItemCountPerPage($collection->pageSize);
                $paginator->setCurrentPageNumber($collection->page);

                $collection->setAttributes(array(
                    'page'        => $paginator->getCurrentPageNumber(),
                    'limit' 	  => $paginator->getItemCountPerPage(),
                    'pages'		  => count($paginator),
                    'count'		  => $paginator->getTotalItemCount()
                ));

            },
            100
           );

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

            ),

        );
    }
}
