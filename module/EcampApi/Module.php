<?php
namespace EcampApi;

use Zend\Mvc\MvcEvent;

use EcampApi\Listener\JsonExceptionStrategy;
use EcampApi\Listener\AuthenticationRequiredExceptionStrategy;
use EcampApi\Camp\CampResourceListener;
use EcampApi\User\UserResourceListener;
use EcampApi\Collaboration\CollaborationResourceListener;
use EcampApi\Period\PeriodResourceListener;
use EcampApi\Day\DayResourceListener;
use EcampApi\Event\EventResourceListener;
use EcampApi\EventInstance\EventInstanceResourceListener;
use EcampApi\EventResp\EventRespResourceListener;
use EcampApi\EventCategory\EventCategoryResourceListener;

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

        /*
         * collection rendering
         * specify here which classes to use for resource rendering in collections
         */
        $event->getTarget()->getEventManager()->getSharedManager()->attach('PhlyRestfully\Plugin\HalLinks', 'renderCollection.resource', function ($e) {
            $collection = $e->getParam('collection');
            $resource = $e->getParam('resource');
            $params = $e->getParams();

            if ($resource instanceof \EcampCore\Entity\Camp) {
                $params['resource']    = new Camp\CampBriefResource($resource);

                return;
            }

            if ($resource instanceof \EcampCore\Entity\User) {
                $params['resource']    = new User\UserBriefResource($resource);

                return;
            }

            if ($resource instanceof \EcampCore\Entity\CampCollaboration) {
                $params['resource']    = new Collaboration\CollaborationResource($resource);

                return;
            }

            if ($resource instanceof \EcampCore\Entity\Period) {
                $params['resource']    = new Period\PeriodBriefResource($resource);

                return;
            }

            if ($resource instanceof \EcampCore\Entity\Day) {
                $params['resource']    = new Day\DayBriefResource($resource);

                return;
            }

            if ($resource instanceof \EcampCore\Entity\Event) {
                $params['resource']    = new Event\EventBriefResource($resource);

                return;
            }

            if ($resource instanceof \EcampCore\Entity\EventInstance) {
                $params['resource']    = new EventInstance\EventInstanceBriefResource($resource);

                return;
            }

            if ($resource instanceof \EcampCore\Entity\EventResp) {
                $params['resource']    = new EventResp\EventRespBriefResource($resource);

                return;
            }

            if ($resource instanceof \EcampCore\Entity\EventCategory) {
                $params['resource']    = new EventCategory\EventCategoryBriefResource($resource);

                return;
            }
        }, 100);

        /*
         * additional paginator attrbiutes for collections
         */
           $event->getTarget()->getEventManager()->getSharedManager()->attach('PhlyRestfully\Plugin\HalLinks', 'renderCollection', function ($e) {
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

            }, 100);
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'EcampApi\Camp\CampResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\Camp');

                    return new CampResourceListener($repository);
                },

                'EcampApi\User\UserResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\User');

                    return new UserResourceListener($repository);
                },

                'EcampApi\Collaboration\CollaborationResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\CampCollaboration');

                    return new CollaborationResourceListener($repository);
                },

                'EcampApi\Period\PeriodResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\Period');

                    return new PeriodResourceListener($repository);
                },

                'EcampApi\Day\DayResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\Day');

                    return new DayResourceListener($repository);
                },

                'EcampApi\Event\EventResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\Event');

                    return new EventResourceListener($repository);
                },

                'EcampApi\EventInstance\EventInstanceResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\EventInstance');

                    return new EventInstanceResourceListener($repository);
                },

                'EcampApi\EventResp\EventRespResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\EventResp');

                    return new EventRespResourceListener($repository);
                },

                'EcampApi\EventCategory\EventCategoryResourceListener' => function ($services) {
                    $repository = $services->get('EcampCore\Repository\EventCategory');

                    return new EventCategoryResourceListener($repository);
                },

            ),

        );
    }
}
