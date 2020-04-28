<?php

namespace eCamp\Core\Plugin;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\EventPlugin;
use eCamp\Core\EntityService\AbstractEntityService;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;
use ZF\ApiProblem\ApiProblem;

abstract class BasePluginService extends AbstractEntityService {
    /** @var string */
    private $eventPluginId;

    /** @var EventPlugin */
    private $eventPlugin;

    public function __construct(
        ServiceUtils $serviceUtils,
        string $entityClassname,
        string $hydratorClassname,
        AuthenticationService $authenticationService,
        string $eventPluginId
    ) {
        parent::__construct($serviceUtils, $entityClassname, $hydratorClassname, $authenticationService);

        $this->eventPluginId = $eventPluginId;
    }

    /**
     * @param mixed $data
     *
     * @throws ORMException
     * @throws NoAccessException
     *
     * @return ApiProblem|BasePluginEntity
     */
    public function create($data) {
        /** @var BasePluginEntity $entity */
        $entity = parent::create($data);

        if (null == $entity->getEventPlugin()) {
            /** @var EventPlugin $eventPlugin */
            $eventPlugin = $this->findEntity(EventPlugin::class, $data->event_plugin_id);
            $entity->setEventPlugin($eventPlugin);
        }

        return $entity;
    }

    /** @return string */
    protected function getEventPluginId() {
        return $this->eventPluginId;
    }

    /** @return EventPlugin */
    protected function getEventPlugin() {
        if (null == $this->eventPlugin) {
            if (null != $this->eventPluginId) {
                $this->eventPlugin = $this->findEntity(EventPlugin::class, $this->eventPluginId);
            }
        }

        return $this->eventPlugin;
    }

    /**
     * @param string $className
     *
     * @return ApiProblem|BasePluginEntity
     */
    protected function createEntity($className) {
        /** @var BasePluginEntity $entity */
        $entity = parent::createEntity($className);

        if ($entity instanceof ApiProblem) {
            return $entity;
        }

        if (null != $this->getEventPlugin()) {
            $entity->setEventPlugin($this->getEventPlugin());
        }

        return $entity;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);

        if (is_subclass_of($this->entityClass, BasePluginEntity::class)) {
            if (null !== $this->eventPluginId) {
                $q->andWhere('row.eventPlugin = :eventPluginId');
                $q->setParameter('eventPluginId', $this->eventPluginId);
            }
        }

        return $q;
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);

        if (is_subclass_of($this->entityClass, BasePluginEntity::class)) {
            if (null !== $this->eventPluginId) {
                $q->andWhere('row.eventPlugin = :eventPluginId');
                $q->setParameter('eventPluginId', $this->eventPluginId);
            }
        }

        return $q;
    }
}
