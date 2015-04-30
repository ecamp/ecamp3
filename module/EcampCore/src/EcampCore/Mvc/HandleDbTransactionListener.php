<?php

namespace EcampCore\Mvc;

use Doctrine\ORM\EntityManager;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface as Events;
use Zend\Mvc\MvcEvent;

class HandleDbTransactionListener extends AbstractListenerAggregate
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function attach(Events $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'openTransaction'), 100);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'closeTransaction'), -10);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'rollbackTransaction'), 10);
    }

    public function openTransaction(MvcEvent $e)
    {
        $this->em->getConnection()->beginTransaction();
    }

    public function closeTransaction(MvcEvent $e)
    {
        $response = $e->getResponse();

        $statusCode = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;
        $statusCode = floor($statusCode / 100);

        switch ($statusCode) {
            case 2:
            case 3:
                $this->commitTransaction($e);
                break;

            case 4:
            case 5:
                $this->rollbackTransaction($e);
                break;

            default:
                throw new \Exception('error in closeTransaction: ' . $statusCode);
        }
    }

    public function commitTransaction(MvcEvent $e)
    {
        if ($this->em->getConnection()->isTransactionActive()) {
            if ($this->em->getConnection()->isRollbackOnly()) {
                $this->rollbackTransaction($e);
            } else {
                $this->em->getConnection()->commit();
            }
        }
    }

    public function rollbackTransaction(MvcEvent $e)
    {
        if ($this->em->getConnection()->isTransactionActive()) {
            $this->em->getConnection()->rollBack();
        }
    }

}
