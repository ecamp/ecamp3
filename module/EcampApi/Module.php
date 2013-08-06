<?php
namespace EcampApi;

use Zend\Mvc\MvcEvent;

use EcampApi\Listener\JsonExceptionStrategy;
use EcampApi\Listener\AuthenticationRequiredExceptionStrategy;
use EcampApi\Camp\CampResourceListener;
use EcampApi\User\UserResourceListener;

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
		}, 100);
        
        
        /*
         * additional paginator attrbiutes for collections
         */
       	$event->getTarget()->getEventManager()->getSharedManager()->attach('PhlyRestfully\Plugin\HalLinks', 'renderCollection', function ($e) {
        		$collection = $e->getParam('collection');
        		$paginator = $collection->collection;
        		
        		if( !$paginator instanceof \Zend\Paginator\Paginator)
        		{
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
    		),
    	
    			
    		
    	);
    }
}
