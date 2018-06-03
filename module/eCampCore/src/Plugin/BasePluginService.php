<?php

namespace eCamp\Core\Plugin;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\EntityService\AbstractEntityService;
use eCamp\Lib\Acl\NoAccessException;
use ZF\ApiProblem\ApiProblem;

abstract class BasePluginService extends AbstractEntityService
{

    /** @var string */
    private $eventPluginId;

    /** @var EventPlugin */
    private $eventPlugin;

    public function __construct(
        $entityClassname,
        $hydratorClassname,
        $eventPluginId
    ) {
        parent::__construct($entityClassname, $hydratorClassname);

        $this->eventPluginId = $eventPluginId;
    }


    /** @return string */
    protected function getEventPluginId()
    {
        return $this->eventPluginId;
    }

    /** @return EventPlugin */
    protected function getEventPlugin()
    {
        if ($this->eventPlugin == null) {
            if ($this->eventPluginId != null) {
                $this->eventPlugin = $this->findEntity(EventPlugin::class, $this->eventPluginId);
            }
        }
        return $this->eventPlugin;
    }


    /**
     * @param string $className
     * @return BasePluginEntity|ApiProblem
     */
    protected function createEntity($className)
    {
        /** @var BasePluginEntity $entity */
        $entity = parent::createEntity($className);

        if ($entity instanceof ApiProblem) {
            return $entity;
        }

        if ($this->getEventPlugin() != null) {
            $entity->setEventPlugin($this->getEventPlugin());
        }

        return $entity;
    }

    protected function fetchQueryBuilder($id)
    {
        $q = parent::fetchQueryBuilder($id);

        if (is_subclass_of($this->entityClass, BasePluginEntity::class)) {
            if ($this->eventPluginId !== null) {
                $q->andWhere('row.eventPlugin = :eventPluginId');
                $q->setParameter('eventPluginId', $this->eventPluginId);
            }
        }

        return $q;
    }

    protected function fetchAllQueryBuilder($params = [])
    {
        $q = parent::fetchAllQueryBuilder($params);

        if (is_subclass_of($this->entityClass, BasePluginEntity::class)) {
            if ($this->eventPluginId !== null) {
                $q->andWhere('row.eventPlugin = :eventPluginId');
                $q->setParameter('eventPluginId', $this->eventPluginId);
            }
        }

        return $q;
    }



    /**
     * @param mixed $data
     * @return BasePluginEntity|ApiProblem
     * @throws ORMException
     * @throws NoAccessException
     */
    public function create($data)
    {
        /** @var BasePluginEntity $entity */
        $entity = parent::create($data);

        if ($entity->getEventPlugin() == null) {
            /** @var EventPlugin $eventPlugin */
            $eventPlugin = $this->findEntity(EventPlugin::class, $data->event_plugin_id);
            $entity->setEventPlugin($eventPlugin);
        }

        return $entity;
    }
}
