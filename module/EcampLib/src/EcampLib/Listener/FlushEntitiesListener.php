<?php

namespace EcampLib\Listener;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\MvcEvent;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\AbstractListenerAggregate;

/**
 * Flush Listener
 *
 * Listens to the MvcEvent::EVENT_FINISH event with a low priority and flushes the page.
 *
 */
class FlushEntitiesListener extends AbstractListenerAggregate
{

    const ERROR_FLUSH = 'error-flush';

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
                MvcEvent::EVENT_FINISH,
                array($this, 'onFinish'),
                -1
        );
    }

    /**
     * MvcEvent::EVENT_DISPATCH event callback
     *
     * @param MvcEvent $event
     */
    public function onFinish(MvcEvent $e)
    {
        try {
            $e->getApplication()->getServiceManager()->get('Doctrine\ORM\EntityManager' )->flush();
        } catch (DBALException $ex) {
            $e->setError(self::ERROR_FLUSH)
            ->setParam('exception', $ex);
            $e->getApplication()->getEventManager()->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $e);
        }
    }
}
