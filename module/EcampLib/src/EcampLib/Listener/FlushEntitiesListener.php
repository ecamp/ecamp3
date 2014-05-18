<?php

namespace EcampLib\Listener;

use Doctrine\DBAL\DBALException;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\Http\Response;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;

/**
 * Flush Listener
 */
class FlushEntitiesListener extends AbstractListenerAggregate
{
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'onFinish'), -1);
    }

    /**
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function onFinish(MvcEvent $e)
    {
        $response = $e->getResponse();
        if ($response instanceof Response && $response->getStatusCode() < 400) {
            try {
                $e->getApplication()->getServiceManager()->get('Doctrine\ORM\EntityManager')->flush();

            } catch (DBALException $ex) {

                $e->getApplication()->getServiceManager()->get('Logger')->err($ex->getMessage());

                $e->setResult(null);
                $e->setParam('exception', $ex);
                $e->setError(Application::ERROR_EXCEPTION);

                $e->getApplication()->getEventManager()->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $e);
                $e->getApplication()->getEventManager()->trigger(MvcEvent::EVENT_RENDER, $e);

            }
        }
    }
}
