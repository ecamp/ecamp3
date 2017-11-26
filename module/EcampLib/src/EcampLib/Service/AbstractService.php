<?php

namespace EcampLib\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use ZF\Apigility\Doctrine\Server\Resource\DoctrineResource;
use ZF\Rest\ResourceInterface;

abstract class AbstractService implements ResourceInterface, ListenerAggregateInterface
{
    /** @var DoctrineResource */
    private $doctrineResource;

    public function __construct(DoctrineResource $doctrineResource)
    {
        $this->doctrineResource = $doctrineResource;
    }

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->doctrineResource->attach($events, $priority);
    }

    public function detach(EventManagerInterface $events)
    {
        $this->detach($events);
    }


    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->doctrineResource->setObjectManager($objectManager);
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->doctrineResource->getObjectManager();
    }


    public function setEventManager(EventManagerInterface $eventManager)
    {
        $this->doctrineResource->setEventManager($eventManager);
    }

    public function getEventManager()
    {
        return $this->doctrineResource->getEventManager();
    }

    public function setEventParams(array $params)
    {
        return $this->doctrineResource->setEventParams($params);
    }

    public function getEventParams()
    {
        return $this->doctrineResource->getEventParams();
    }

    public function setEventParam($name, $value)
    {
        return $this->doctrineResource->setEventParam($name, $value);
    }

    public function getEventParam($name, $default = null)
    {
        return $this->doctrineResource->getEventParam($name, $default);
    }

    public function setEntityClass($className)
    {
        return $this->doctrineResource->setEntityClass($className);
    }

    public function setCollectionClass($className)
    {
        return $this->doctrineResource->setCollectionClass($className);
    }


    public function create($data)
    {
        return $this->doctrineResource->create($data);
    }

    public function update($id, $data)
    {
        return $this->doctrineResource->update($id, $data);
    }

    public function replaceList($data)
    {
        return $this->doctrineResource->replaceList($data);
    }

    public function patch($id, $data)
    {
        return $this->doctrineResource->patch($id, $data);
    }

    public function delete($id)
    {
        return $this->doctrineResource->delete($id);
    }

    public function deleteList($data = null)
    {
        return $this->doctrineResource->deleteList($data);
    }

    public function fetch($id)
    {
        return $this->doctrineResource->fetch($id);
    }

    public function fetchAll()
    {
        return $this->doctrineResource->fetchAll();
    }

}
